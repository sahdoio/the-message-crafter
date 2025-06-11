<?php

namespace App\Services\Messenger\Contracts;

use App\Domain\Contact\DTOs\SendMessageInputDTO;
use App\Domain\Contact\VOs\MessageBody;

interface IMessenger
{
    function send(MessageBody|SendMessageInputDTO $data): bool;
}
