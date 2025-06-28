<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTime;
use Domain\Contact\Enums\ConversationStatus;
use Domain\Shared\Events\HasDomainEvents;
use DomainException;

class Conversation
{
    use HasDomainEvents;

    public function __construct(
        public ?int $id = null,
        public int $contactId,
        public string $status = ConversationStatus::ACTIVE->value,
        public ?string $startedAt = null,
        public ?string $finishedAt = null,
        /** @var Message[] */
        public array $messages = []
    ) {
        $this->startedAt ??= new DateTime()->format('Y-m-d H:i:s');
    }

    public function isActive(): bool
    {
        return $this->status === ConversationStatus::ACTIVE->value;
    }

    public function finish(): void
    {
        $this->status = ConversationStatus::FINISHED->value;
        $this->finishedAt = new DateTime()->format('Y-m-d H:i:s');
    }

    public function addMessage(Message $message): void
    {
        if (!$this->isActive()) {
            throw new DomainException("Cannot add message to closed conversation.");
        }

        $this->messages[] = $message;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }
}
