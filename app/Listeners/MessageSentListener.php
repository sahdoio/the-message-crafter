<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\SendMessageToProvider;
use App\Events\MessageSentEvent;
use App\Exceptions\ResourceNotFoundException;

class MessageSentListener
{
    public function __construct(
        protected SendMessageToProvider $action
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageSentEvent $event): void
    {
        $this->action->exec($event->messageSent->messageId);
    }
}
