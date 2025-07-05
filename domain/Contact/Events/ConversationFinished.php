<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\DomainEvent;

readonly class ConversationFinished extends DomainEvent
{
    public function __construct(
        public int $conversationId,
        public int $contactId,
        public string $finishedAt,
    )
    {
        parent::__construct('ConversationFinished');
    }
}
