<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\SendMessageToProvider;
use Domain\Contact\Events\MessageSentEvent;

class SendMessageToProviderListener
{
    public function __construct(
        protected SendMessageToProvider $action
    ) {}

    public function handle(MessageSentEvent $event): void
    {
        $this->action->execute($event->messageId);
    }
}
