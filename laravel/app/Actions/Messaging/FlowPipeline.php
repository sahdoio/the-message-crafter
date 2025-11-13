<?php

declare(strict_types=1);

namespace App\Actions\Messaging;

use App\DTOs\MessageFlowInputDTO;
use Domain\Messaging\Entities\Conversation;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

class FlowPipeline
{
    public function __construct(protected Pipeline $pipeline) {}

    public function process(
        MessageFlowInputDTO $input,
        Conversation        $conversation,
        array               $steps
    ): void
    {
        Log::info('FlowPipeline - Processing steps', [
            'conversation_id' => $conversation->id,
            'steps' => $steps,
        ]);

        $this->pipeline
            ->send($input)
            ->through($steps)
            ->then(function ($data) use ($conversation) {
                Log::info('FlowPipeline - Steps processed successfully', [
                    'conversation_id' => $conversation->id,
                    'final_step' => get_class($data),
                ]);
            });
    }
}
