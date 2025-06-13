<?php

declare(strict_types=1);

namespace App\UseCases\Contact;

use App\DTOs\MessageFlowInputDTO;
use App\DTOs\ProcessMessageCallbackInputDTO;
use App\Facades\Repository;
use App\Models\MessageButton;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessMessageCallback
{
    const CACHE_PREFIX = 'message-callback-';
    const CACHE_TIME = 86400; // 24 hours in seconds

    public function __construct(protected FlowStrategyResolver $flowStrategyResolver) {}

    public function exec(ProcessMessageCallbackInputDTO $data): void
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

            Log::info('ProcessMessageCallback - Incoming Message!', $data->values());

            if (is_array($errors) && count($errors) > 0) {
                Log::error('ProcessMessageCallback', ['errors' => $errors]);
                return;
            }

            $hasButtonReply = is_array($buttonReply) && count($buttonReply) > 0;
            if (false === $hasButtonReply){
                Log::warning('ProcessMessageCallback', ['errors' => 'Button reply is missing']);
                return;
            }

            // If the button is inside a template, the webhook response structure is different
            // So we need to check if the button is inside a template or not
            // For interactive message the button id comes inside "id" and title inside "title"
            // For templates the button id comes inside "payload" and title inside "text"
            $whatsappButtonId = $this->extractButtonId($buttonReply);
            $whatsappExtraInfo = $this->extractExtraInfo($buttonReply);
            $replyAction = Arr::get($buttonReply, 'title') ?? Arr::get($buttonReply, 'text');

            Log::info('ProcessMessageCallback - action selected', [
                'replyAction' => $replyAction,
            ]);

            $message = Repository::setEntity(MessageButton::class)->findOne([
                'button_id' => $whatsappButtonId,
            ]);

            $flowStrategy = $this->flowStrategyResolver->resolve($message->id);
            $flowStrategy->handle(new MessageFlowInputDTO(
                contactPhone: $recipientId,
                buttonId: $whatsappButtonId,
                replyAction: $replyAction,
                extraInfo: $whatsappExtraInfo,
            ));

            Log::info('ProcessMessageCallback - success', [
                'recipientId' => $recipientId,
                'button' => $message,
            ]);
        } catch (\Exception $exception) {
            Log::error('ProcessMessageCallback', ['error' => $exception]);

            // We don't want to throw the exception here, because we don't want meta to receive the error
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
