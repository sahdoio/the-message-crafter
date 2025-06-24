<?php

namespace Domain\Shared\Events;

interface IDomainEventBus
{
    public function publish(DomainEvent $event): void;

    public function publishAll(array $events): void;
}
