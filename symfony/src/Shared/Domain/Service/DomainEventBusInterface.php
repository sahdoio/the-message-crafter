<?php

declare(strict_types=1);

namespace Shared\Domain\Service;

use Shared\Domain\Event\DomainEvent;

interface DomainEventBusInterface
{
    public function publish(DomainEvent $event): void;

    /**
     * @param DomainEvent[] $events
     */
    public function publishAll(array $events): void;

    public function publishEntity(object $entity): void;

    /**
     * @param object[] $entities
     */
    public function publishEntities(array $entities): void;
}
