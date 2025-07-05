<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\ProcessMessageCallbackInputDTO;
use App\Facades\DomainEventBus;
use App\Facades\Repository;
use App\HasCache;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
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
            $errors = $data->errors;

            if (!$messageId || !$recipientId) {
                Log::info('ProcessMessageCallback', ['messages' => 'MessageId or recipientId not found']);
                return;
            }

            if (!$this->hasCache($messageId)) {
                Log::info('ProcessMessageCallback - Cache found! Ignoring duplicated message', $data->values());
                return;
            }

            if (is_array($errors) && count($errors) > 0) {
                Log::error('ProcessMessageCallback', ['errors' => $errors]);
                return;
            }

            $hasButtonReply = is_array($buttonReply) && count($buttonReply) > 0;
            if (!$hasButtonReply) {
                Log::warning('ProcessMessageCallback', ['errors' => 'Button reply is missing']);
                return;
            }

            Log::info('ProcessMessageCallback - Message Info: ', ['message_id' => $messageId, 'recipient_id' => $recipientId, 'button_reply' => $buttonReply]);

            $whatsappButtonId = $this->extractButtonId($buttonReply);
            $whatsappExtraInfo = $this->extractExtraInfo($buttonReply);
            $replyAction = Arr::get($buttonReply, 'title') ?? Arr::get($buttonReply, 'text');

            /**
             * @var MessageButton $messageButton
             */
            $messageButton = Repository::for(MessageButton::class)->findOne(['button_id' => $whatsappButtonId]);

            if (!$messageButton) {
                Log::warning('ProcessMessageCallback - MessageButton not found', ['button_id' => $whatsappButtonId]);
                return;
            }

            /**
             * @var Message $message
             */
            $message = Repository::for(Message::class)->findOne(['id' => $messageButton->messageId]);

            if (!$message) {
                Log::warning('ProcessMessageCallback - Message not found', ['message_id' => $messageButton->messageId]);
                return;
            }

            /**
             * @var Conversation $conversation
             */
            $conversation = Repository::for(Conversation::class)->findOne(['id' => $message->conversationId]);

            if (!$conversation) {
                Log::warning('ProcessMessageCallback - Conversation not found', ['conversation_id' => $message->conversationId]);
                return;
            }

            /**
             * @var Contact $contact
             */
            $contact = Repository::for(Contact::class)->findOne(['id' => $conversation->contactId]);

            $contact->messageReceived(
                conversationId: $conversation->id,
                messageId: $message->id,
                buttonId: $whatsappButtonId,
                replyAction: $replyAction,
                extraInfo: $whatsappExtraInfo
            );

            DomainEventBus::publishEntity($contact);

            Log::info('ProcessMessageCallback - ButtonClicked dispatched');
        } catch (\Exception $exception) {
            Log::error('ProcessMessageCallback', ['error' => $exception]);
        }
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
        $extraInfo = $result[1] ?? null;
        return $extraInfo && json_validate($extraInfo) ? json_decode($extraInfo, true) : [];
    }
}
