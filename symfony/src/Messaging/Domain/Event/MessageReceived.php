<?php

declare(strict_types=1);

namespace Messaging\Domain\Event;

use Shared\Domain\Event\DomainEvent;

final readonly class MessageReceived extends DomainEvent
{
    public function __construct(
        private int $conversationId,
        private int $messageId,
        private ?string $contactPhone,
        private string $replyAction,
        private ?string $buttonId = null,
        private array $extraInfo = []
    ) {
        parent::__construct();
    }

    public function conversationId(): int
    {
        return $this->conversationId;
    }

    public function messageId(): int
    {
        return $this->messageId;
    }

    public function contactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function replyAction(): string
    {
        return $this->replyAction;
    }

    public function buttonId(): ?string
    {
        return $this->buttonId;
    }

    public function extraInfo(): array
    {
        return $this->extraInfo;
    }

    public function eventName(): string
    {
        return 'messaging.message.received';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'conversation_id' => $this->conversationId,
            'message_id' => $this->messageId,
            'contact_phone' => $this->contactPhone,
            'reply_action' => $this->replyAction,
            'button_id' => $this->buttonId,
            'extra_info' => $this->extraInfo,
        ]);
    }
}
