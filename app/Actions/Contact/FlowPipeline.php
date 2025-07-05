<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\MessageFlowInputDTO;
use Domain\Contact\Entities\Conversation;
use Illuminate\Pipeline\Pipeline;

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

        $this->pipeline
            ->send($input)
            ->through($steps)
            ->then(fn() => null); // fim da pipeline
    }

    private function skipProcessedSteps(Conversation $conversation, array $steps): array
    {
        $startFrom = $conversation->currentStep;

        if (!$startFrom) return $steps;

        $index = array_search($startFrom, $steps);
        if ($index === false) return $steps;

        return array_slice($steps, $index);
    }
}
