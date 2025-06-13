<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\DTOs\MessageFlowInputDTO;
use Domain\Contact\Contracts\IMessageFlow;

class AIFlowStrategy implements IMessageFlow
{

    function handle(MessageFlowInputDTO $data): void
    {
        // TODO: Implement handle() method.
    }
}
