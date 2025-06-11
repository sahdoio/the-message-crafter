<?php

declare(strict_types=1);

namespace App\Services\Messenger\Email;

use App\Domain\Contact\DTOs\SendMessageInputDTO;
use App\Domain\Contact\VOs\MessageBody;
use App\Services\Messenger\Contracts\IMessenger;

class Messenger implements IMessenger
{
    function send(MessageBody|SendMessageInputDTO $data): bool
    {
        // Sendgrid Implementation
        return true;
    }
}
