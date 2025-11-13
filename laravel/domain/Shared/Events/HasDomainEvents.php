<?php

namespace Domain\Shared\Events;

namespace Domain\Shared\Events;

trait HasDomainEvents
{
    /** @var array<callable|DomainEvent> */
    protected array $domainEvents = [];

    /**
     * Returns and clears the domain events.
     *
     * @param mixed ...$params
     * @return array
     */
    public function releaseDomainEvents(mixed ...$params): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return array_map(function ($event) use ($params) {
            return is_callable($event) ? $event(...$params) : $event;
        }, $events);
    }

    /**
     * Add a domain event to the list.
     *
     * @param DomainEvent|callable $event
     * @return void
     */
    public function recordDomainEvent(DomainEvent|callable $event): void
    {
        $this->domainEvents[] = $event;
    }
}
