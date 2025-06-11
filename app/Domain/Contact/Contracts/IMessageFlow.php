<?php

namespace App\Domain\Contact\Contracts;

use App\Domain\Contact\DTOs\MessageFlowInputDTO;

interface IMessageFlow
{
    function handle(MessageFlowInputDTO $data): void;
}
