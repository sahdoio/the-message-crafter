<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

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
                AskName::class,
                StoreName::class,
                ConfirmStart::class,
            ])
            ->then(fn() => null);
    }
}
