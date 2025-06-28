<?php

declare(strict_types=1);

namespace App\Services\Messenger\Email;

use App\Services\Messenger\Contracts\IMessenger;
use Domain\Contact\Entities\Message;

class SendGrid implements IMessenger
{
    function send(Message $message): bool
    {
        // TODO: Sendgrid Implementation
        return true;
    }
}
