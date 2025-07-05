<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\DomainEvent;

readonly class MessageReceived extends DomainEvent
{
    public function __construct(
        public int $conversationId,
        public int $messageId,
        public string $contactPhone,
        public string $buttonId,
        public string $replyAction,
        public array $extraInfo = [],
    ) {
        parent::__construct('MessageReceived');
    }
}
