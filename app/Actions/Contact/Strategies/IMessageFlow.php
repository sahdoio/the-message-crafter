<?php

namespace App\Actions\Contact\Strategies;

use App\DTOs\MessageFlowInputDTO;

interface IMessageFlow
{
    public function handle(MessageFlowInputDTO $data): void;
}
