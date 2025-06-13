<?php

declare(strict_types=1);

namespace App\Services\Messenger\Email;

use App\DTOs\SendMessageInputDTO;
use App\Services\Messenger\Contracts\IMessenger;
use Domain\Contact\ValueObjects\MessageBody;

class Messenger implements IMessenger
{
    function send(MessageBody|SendMessageInputDTO $data): bool
    {
        // Sendgrid Implementation
        return true;
    }
}
