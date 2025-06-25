<?php

namespace App\Actions\Contact\Strategies;

use App\DTOs\MessageFlowInputDTO;

interface IMessageFlow
{
    function handle(MessageFlowInputDTO $data): void;
}
