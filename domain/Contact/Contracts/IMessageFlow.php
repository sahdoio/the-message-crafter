<?php

namespace Domain\Contact\Contracts;

use App\DTOs\MessageFlowInputDTO;

interface IMessageFlow
{
    function handle(MessageFlowInputDTO $data): void;
}
