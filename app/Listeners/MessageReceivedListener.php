<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\HandleMessageReceived;
use App\Events\MessageReceivedEvent;
use Exception;

class MessageReceivedListener
{
    public function __construct(
        protected HandleMessageReceived $action
    ) {}

    /**
     * @throws Exception
     */
    public function handle(MessageReceivedEvent $event): void
    {
        $this->action->handle($event->messageReceived);
    }
}
