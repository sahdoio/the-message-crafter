<?php

declare(strict_types=1);

namespace App\Domain\Contact\UseCases\Strategies;

use App\Domain\Contact\Contracts\IMessageFlow;
use App\Domain\Contact\DTOs\MessageFlowInputDTO;

class AIFlowStrategy implements IMessageFlow
{

    function handle(MessageFlowInputDTO $data): void
    {
        // TODO: Implement handle() method.
    }
}
