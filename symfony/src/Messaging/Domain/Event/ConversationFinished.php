<?php

declare(strict_types=1);

namespace Messaging\Domain\Event;

use Shared\Domain\Event\DomainEvent;

final readonly class ConversationFinished extends DomainEvent
{
    public function __construct(
        private int $conversationId,
        private int $contactId,
        private string $finishedAt
    ) {
        parent::__construct();
    }

    public function conversationId(): int
    {
        return $this->conversationId;
    }

    public function contactId(): int
    {
        return $this->contactId;
    }

    public function finishedAt(): string
    {
        return $this->finishedAt;
    }

    public function eventName(): string
    {
        return 'messaging.conversation.finished';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'conversation_id' => $this->conversationId,
            'contact_id' => $this->contactId,
            'finished_at' => $this->finishedAt,
        ]);
    }
}
