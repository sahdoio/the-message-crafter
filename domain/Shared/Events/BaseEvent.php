<?php

declare(strict_types=1);

namespace Domain\Shared\Events;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

readonly class BaseEvent
{
    public string $eventId;
    public DateTimeImmutable $createdAt;

    public function __construct(
        public string $eventName
    ) {
        $this->eventId = Uuid::uuid7()->toString();
        $this->createdAt = new DateTimeImmutable();
    }
}
