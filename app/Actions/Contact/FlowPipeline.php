<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\MessageFlowInputDTO;
use Domain\Contact\Entities\Conversation;
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
        $steps = $this->skipProcessedSteps($conversation, $steps);

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

    private function skipProcessedSteps(Conversation $conversation, array $steps): array
    {
        $startFrom = $conversation->currentStep;

        if (!$startFrom) return $steps;

        $index = array_search($startFrom, $steps);

        if ($index === false) return $steps;

        return array_slice($steps, $index + 1);
    }
}
