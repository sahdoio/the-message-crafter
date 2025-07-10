<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\FilterOptionsDTO;
use App\DTOs\ProcessMessageCallbackInputDTO;
use App\Facades\DomainEventBus;
use App\Facades\Repository;
use App\Support\Cache\HasCache;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Enums\MessageStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ProcessMessageCallback
{
    use HasCache;

    public function handle(ProcessMessageCallbackInputDTO $data): void
    {
        try {
            $messageId = $data->messageId;
            $recipientId = $data->recipientId;
            $buttonReply = $data->buttonReply;
            $text = $data->text;
            $errors = $data->errors;

            if (!$messageId || !$recipientId) {
                Log::info('ProcessMessageCallback - Missing messageId or recipientId');
                return;
            }

            if (!$this->hasCache($messageId)) {
                Log::info('ProcessMessageCallback - Duplicate message detected, skipping', $data->values());
                return;
            }

            if (!empty($errors)) {
                Log::error('ProcessMessageCallback - Errors present', ['errors' => $errors]);
                return;
            }

            if (!empty($buttonReply)) {
                $this->handleButtonReply($messageId, $buttonReply);
                return;
            }

            if (!empty($text)) {
                $this->handleTextMessage($messageId, $recipientId, $text);
                return;
            }

            Log::warning('ProcessMessageCallback - No valid payload detected');
        } catch (\Throwable $exception) {
            Log::error('ProcessMessageCallback - Unhandled error', ['exception' => $exception]);
        }
    }

    private function handleButtonReply(string $messageId, array $buttonReply): void
    {
        $buttonId = $this->extractButtonId($buttonReply);
        $extraInfo = $this->extractExtraInfo($buttonReply);
        $replyText = Arr::get($buttonReply, 'title') ?? Arr::get($buttonReply, 'text');

        /** @var MessageButton $messageButton */
        $messageButton = Repository::for(MessageButton::class)->findOne(['button_id' => $buttonId]);
        /** @var Message $message */
        $message = Repository::for(Message::class)->findOne(['id' => $messageButton->messageId]);
        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findOne(['id' => $message->conversationId]);

        if (!$conversation) {
            Log::warning('ProcessMessageCallback - Conversation not found', ['conversation_id' => $message->conversationId]);
            return;
        }

        /** @var Contact $contact */
        $contact = Repository::for(Contact::class)->findOne(['id' => $conversation->contactId]);

        $contact->messageReceived(
            conversationId: $conversation->id,
            messageId: $message->id,
            replyAction: $replyText,
            buttonId: $buttonId,
            extraInfo: $extraInfo
        );

        DomainEventBus::publishEntity($contact);

        Log::info('ProcessMessageCallback - Button reply processed successfully');
    }

    private function handleTextMessage(string $messageId, string $recipientId, string $text): void
    {
        Log::info('ProcessMessageCallback - Processing text message', [
            'message_id' => $messageId,
            'recipient_id' => $recipientId,
            'text' => $text,
        ]);

        /** @var Contact $contact */
        $contact = Repository::for(Contact::class)->findOne(['phone' => $recipientId]);
        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findOne([
            'contact_id' => $contact->id,
            'status' => ConversationStatus::ACTIVE->value,
            'finished_at' => null,
        ]);
        /** @var Message $message */
        $message = Repository::for(Message::class)->findOne([
            'conversation_id' => $conversation->id,
            'status' => MessageStatus::SENT->value,
        ], new FilterOptionsDTO(
            orderBy: 'created_at',
            orderDirection: 'desc',
        ));

        $contact->messageReceived(
            conversationId: $conversation->id,
            messageId: $message->id,
            replyAction: $text,
        );

        DomainEventBus::publishEntity($contact);

        Log::info('ProcessMessageCallback - Text message routed successfully');
    }

    private function extractButtonId(array $buttonReply): ?string
    {
        $result = Arr::get($buttonReply, 'id') ?? Arr::get($buttonReply, 'payload');
        $result = explode('|', $result);
        return $result[0] ?? null;
    }

    private function extractExtraInfo(array $buttonReply): array
    {
        $result = Arr::get($buttonReply, 'id') ?? Arr::get($buttonReply, 'payload');
        $result = explode('|', $result);
        $extra = $result[1] ?? null;

        return $extra && json_validate($extra) ? json_decode($extra, true) : [];
    }
}
