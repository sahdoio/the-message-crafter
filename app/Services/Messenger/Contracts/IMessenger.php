<?php

namespace App\Services\Messenger\Contracts;

use Domain\Contact\Entities\Message;

interface IMessenger
{
    public function send(Message $message): bool;
}
