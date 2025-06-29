<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\ProcessMessageCallbackInputDTO;
use App\Facades\DomainEventBus;
use App\Facades\Repository;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessMessageCallback
{
    const string CACHE_PREFIX = 'message-callback-';
    const int CACHE_TIME = 86400; // 24 hours in seconds

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

            if (!$this->verifyCache($messageId)) {
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

            $whatsappButtonId = $this->extractButtonId($buttonReply);
            $whatsappExtraInfo = $this->extractExtraInfo($buttonReply);
            $replyAction = Arr::get($buttonReply, 'title') ?? Arr::get($buttonReply, 'text');

            /**
             * @var MessageButton $messageButton
             */
            $messageButton = Repository::for(MessageButton::class)->findOne(['button_id' => $whatsappButtonId]);
            /**
             * @var Message $message
             */
            $message = Repository::for(Message::class)->findOne(['id' => $messageButton->messageId]);
            /**
             * @var Conversation $conversation
             */
            $conversation = Repository::for(Conversation::class)->findOne(['id' => $message->conversationId]);

            /**
             * @var Contact $contact
             */
            $contact = Repository::for(Contact::class)->findOne(['id' => $conversation->contactId]);

            $contact->buttonClicked(
                messageId: $messageId,
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

    private function verifyCache(string $messageId): bool
    {
        $cacheKey = self::CACHE_PREFIX . $messageId;
        $cacheValue = Cache::get($cacheKey);
        if ($cacheValue) {
            return false;
        }
        Cache::put($cacheKey, true, self::CACHE_TIME);
        return true;
    }
}
