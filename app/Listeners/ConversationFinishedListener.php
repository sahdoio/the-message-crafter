<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\HandleConversationFinished;
use App\Events\ConversationFinishedEvent;
use App\Exceptions\ResourceNotFoundException;

class ConversationFinishedListener
{
    public function __construct(
        protected HandleConversationFinished $action
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(ConversationFinishedEvent $event): void
    {
        $this->action->handle($event);
    }
}
