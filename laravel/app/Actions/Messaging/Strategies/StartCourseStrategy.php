<?php

declare(strict_types=1);

namespace App\Actions\Messaging\Strategies;

use App\Actions\Messaging\FlowPipeline;
use App\Actions\Messaging\Pipes\AskCourse;
use App\Actions\Messaging\Pipes\AskEmail;
use App\Actions\Messaging\Pipes\SaveEmail;
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
                AskEmail::class,
                SaveEmail::class,
            ]
        );
    }
}

