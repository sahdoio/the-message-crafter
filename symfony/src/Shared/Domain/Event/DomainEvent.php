<?php

declare(strict_types=1);

namespace Shared\Domain\Event;

use DateTimeImmutable;

abstract readonly class DomainEvent
{
    public function __construct(
        private string $eventId = '',
        private DateTimeImmutable $occurredOn = new DateTimeImmutable()
    ) {
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    abstract public function eventName(): string;

    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'occurred_on' => $this->occurredOn->format('Y-m-d H:i:s'),
            'event_name' => $this->eventName(),
        ];
    }
}
