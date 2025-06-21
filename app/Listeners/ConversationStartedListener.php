<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\SendMessage;
use App\Events\ConversationStartedEvent;
use App\Exceptions\ResourceNotFoundException;

class ConversationStartedListener
{
    public function __construct(
        protected SendMessage $action
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(ConversationStartedEvent $event): void
    {
        $this->action->exec($event->messageSent->messageId);
    }
}
