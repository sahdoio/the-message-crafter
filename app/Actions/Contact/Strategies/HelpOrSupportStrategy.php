<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\Actions\Contact\FlowPipeline;
use App\DTOs\MessageFlowInputDTO;
use Domain\Contact\Entities\Conversation;

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
