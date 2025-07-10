<?php

declare(strict_types=1);

namespace App\Actions\Contact\Pipes;

use App\DTOs\MessageFlowInputDTO;
use App\Facades\Repository;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Illuminate\Support\Facades\Log;
use Closure;

abstract class ConversationPipe
{
    public function verifyStep(MessageFlowInputDTO $data, Closure $next): true|Closure
    {
        if ($data->conversation->currentStep === static::class) {
            Log::info('ConversationPipe - Skipping step', [
                'conversation_id' => $data->conversation->id,
                'current_step' => $data->conversation->currentStep,
                'step' => static::class,
            ]);
            return $next($data);
        }

        $alreadySent = Repository::for(Message::class)->exists([
            'conversation_id' => $data->conversation->id,
            'conversation_step' => static::class,
        ]);

        if ($alreadySent) {
            Log::info('ConversationPipe - Message already sent', [
                'conversation_id' => $data->conversation->id,
                'step' => static::class,
            ]);
            return $next($data);
        }

        Log::info('ConversationPipe - Proceeding with step', [
            'conversation_id' => $data->conversation->id,
            'step' => static::class,
        ]);

        return true;
    }

    public function updateStep(MessageFlowInputDTO $data): void
    {
        Log::info('ConversationPipe - Updating step', [
            'conversation_id' => $data->conversation->id,
            'step' => static::class,
        ]);

        Repository::for(Conversation::class)->update($data->conversation->id, [
            'current_step' => static::class,
        ]);
    }
}
