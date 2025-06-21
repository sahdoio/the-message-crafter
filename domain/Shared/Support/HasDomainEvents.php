<?php

namespace Domain\Shared\Support;

trait HasDomainEvents
{
    protected array $domainEvents = [];

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
