<?php

namespace Domain\Shared\Events;

interface IDomainEventBus
{
    public function publish(object $entity): void;

    public function publishAll(array $entities): void;
}
