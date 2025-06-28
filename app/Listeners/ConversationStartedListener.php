<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\SendStartMessage;
use App\Events\ConversationStartedEvent;
use App\Exceptions\ResourceNotFoundException;

class ConversationStartedListener
{
    public function __construct(
        protected SendStartMessage $action
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(ConversationStartedEvent $event): void
    {
        $this->action->handle($event->conversationStarted->conversationId);
    }
}
