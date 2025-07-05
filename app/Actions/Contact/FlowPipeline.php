<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Facades\Repository;
use Domain\Contact\Entities\Conversation;
use Illuminate\Pipeline\Pipeline;
use App\DTOs\MessageFlowInputDTO;

class FlowPipeline
{
    public function __construct(protected Pipeline $pipeline) {}

    public function process(
        MessageFlowInputDTO $input,
        Conversation        $conversation,
        array               $steps
    ): void
    {
        $startFrom = $conversation->currentStep;
        $found = !$startFrom;

        foreach ($steps as $step) {
            if (!$found && $step !== $startFrom) {
                continue;
            }

            $found = true;

            // Pipes
            app($step)->handle($input);

            $conversation->advanceToStep($step);
            Repository::for(Conversation::class)->persistEntity($conversation);
        }
    }
}
