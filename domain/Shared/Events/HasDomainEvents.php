<?php

namespace Domain\Shared\Events;

namespace Domain\Shared\Events;

trait HasDomainEvents
{
    /** @var array<callable|DomainEvent> */
    protected array $domainEvents = [];

    public function releaseDomainEvents(mixed ...$params): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return array_map(function ($event) use ($params) {
            return is_callable($event) ? $event(...$params) : $event;
        }, $events);
    }

    public function recordDomainEvent(DomainEvent|callable $event): void
    {
        $this->domainEvents[] = $event;
    }
}
