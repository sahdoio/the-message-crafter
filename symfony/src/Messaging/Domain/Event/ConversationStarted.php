<?php

declare(strict_types=1);

namespace Messaging\Domain\Event;

use Shared\Domain\Event\DomainEvent;

final readonly class ConversationStarted extends DomainEvent
{
    public function __construct(
        private int $conversationId,
        private int $contactId,
        private array $content = []
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

    public function content(): array
    {
        return $this->content;
    }

    public function eventName(): string
    {
        return 'messaging.conversation.started';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'conversation_id' => $this->conversationId,
            'contact_id' => $this->contactId,
            'content' => $this->content,
        ]);
    }
}
