<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\Actions\Contact\FlowPipeline;
use App\Actions\Contact\Pipes\AskCourse;
use App\DTOs\MessageFlowInputDTO;

class StartCourseStrategy implements IMessageFlow
{
    public function __construct(
        protected FlowPipeline $flow
    ) {}

    public function handle(MessageFlowInputDTO $data): void
    {
        $this->flow->process(
            input: $data,
            conversation: $data->conversation,
            steps: [
                AskCourse::class,
                // More tasks soon
            ]
        );
    }
}

