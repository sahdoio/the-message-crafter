<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\Actions\Contact\Pipes\AskCourse;
use App\DTOs\MessageFlowInputDTO;
use Illuminate\Pipeline\Pipeline;

class StartCourseStrategy implements IMessageFlow
{
    public function __construct(
        protected Pipeline $pipeline
    ) {}

    public function handle(MessageFlowInputDTO $data): void
    {
        $this->pipeline
            ->send($data)
            ->through([
                AskCourse::class
            ])
            ->then(fn() => null);
    }
}
