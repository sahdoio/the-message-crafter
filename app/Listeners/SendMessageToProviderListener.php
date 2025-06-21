<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Contact\SendMessageToProvider;
use App\Exceptions\ResourceNotFoundException;
use Domain\Contact\Events\MessageSent;

class SendMessageToProviderListener
{
    public function __construct(
        protected SendMessageToProvider $action
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageSent $event): void
    {
        $this->action->exec($event->messageId);
    }
}
