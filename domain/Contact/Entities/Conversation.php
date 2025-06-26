<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTimeImmutable;
use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Events\ConversationStarted;
use Domain\Shared\Events\HasDomainEvents;

class Conversation
{
    use HasDomainEvents;

    public function __construct(
        public ?int $id = null,
        public ?int $contactId = null,
        public string $status = ConversationStatus::ACTIVE->value,
        public ?DateTimeImmutable $startedAt = null,
        public ?DateTimeImmutable $closedAt = null,
        /** @var Message[] */
        public array $messages = []
    ) {
        $this->startedAt ??= new DateTimeImmutable();

        if ($status === ConversationStatus::ACTIVE->value) {
            $this->recordDomainEvent(new ConversationStarted(
                conversationId: $this->id,
                contactId: $this->contactId,
            ));
        }
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function close(): void
    {
        $this->status = 'closed';
        $this->closedAt = new DateTimeImmutable();
    }

    public function addMessage(Message $message): void
    {
        if (!$this->isActive()) {
            throw new \DomainException("Cannot add message to closed conversation.");
        }

        $this->messages[] = $message;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }
}
