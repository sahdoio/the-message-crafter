<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Events\MessageSentEvent;

class Contact
{
    private function __construct(
        public int $id,
        public string $name,
        public string $email,
        public array $domainEvents = []
    ) {}

    public function sendMessage(array $content): Message
    {
        $message = new Message(
            contactId: $this->id,
            status: MessageStatus::SENT->value
        );

        $this->domainEvents[] = fn(Message $persistedMessage) => (
            new MessageSentEvent(
                messageId: $persistedMessage->id,
                contactId: $this->id,
                content: $content
            )
        );

        return $message;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
