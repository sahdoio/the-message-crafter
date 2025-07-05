<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\Actions\Contact\FlowPipeline;
use App\Actions\Contact\Pipes\AskCourse;
use App\Actions\Contact\Pipes\AskEmail;
use App\Actions\Contact\Pipes\SaveEmail;
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

