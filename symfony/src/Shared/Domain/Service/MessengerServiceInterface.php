<?php

declare(strict_types=1);

namespace Shared\Domain\Service;

use Messaging\Domain\Entity\Message;

interface MessengerServiceInterface
{
    public function send(Message $message): bool;
}
