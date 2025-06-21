<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Events\ConversationStarted;
use Domain\Shared\Support\HasDomainEvents;

class Contact
{
    use HasDomainEvents;

    private function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $phone,
    ) {}

    public function startConversation(): Message
    {
        $message = new Message(
            contactId: $this->id,
            status: MessageStatus::SENT->value
        );

        $this->domainEvents[] = fn(Message $persistedMessage) => (
            new ConversationStarted(
                messageId: $persistedMessage->id,
                contactId: $this->id,
            )
        );

        return $message;
    }
}
