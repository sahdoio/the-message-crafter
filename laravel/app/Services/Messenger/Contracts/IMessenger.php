<?php

namespace App\Services\Messenger\Contracts;

use Domain\Messaging\Entities\Message;

interface IMessenger
{
    public function send(Message $message): bool;
}
