<?php

namespace App\Actions\Messaging\Strategies;

use App\DTOs\MessageFlowInputDTO;

interface IMessageFlow
{
    public function handle(MessageFlowInputDTO $data): void;
}
