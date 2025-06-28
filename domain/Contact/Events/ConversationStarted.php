<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\DomainEvent;

readonly class ConversationStarted extends DomainEvent
{
    public function __construct(
        public int   $conversationId,
        public int   $contactId,
        public array $content = [],
    )
    {
        parent::__construct('ConversationStarted');
    }
}
