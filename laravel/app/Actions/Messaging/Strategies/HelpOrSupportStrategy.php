<?php

declare(strict_types=1);

namespace App\Actions\Messaging\Strategies;

use App\Actions\Messaging\FlowPipeline;
use App\DTOs\MessageFlowInputDTO;

class HelpOrSupportStrategy implements IMessageFlow
{
    public function __construct(
        protected FlowPipeline $flow
    ) {}
    function handle(MessageFlowInputDTO $data): void
    {
        // TODO: Implement handle() method.
    }
}
