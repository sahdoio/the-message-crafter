<?php

namespace App\Services\Messenger\Contracts;

use App\DTOs\SendMessageInputDTO;
use Domain\Contact\ValueObjects\MessageBody;

interface IMessenger
{
    function send(MessageBody|SendMessageInputDTO $data): bool;
}
