<?php

declare(strict_types=1);

namespace Domain\Shared\Events;

use Closure;

interface IDomainEventBus
{
    /**
     * Publishes a single event (callable or object).
     */
    public function publish(DomainEvent|Closure $event, array $paramsMap = []): void;

    /**
     * Publishes a list of events (can be callables or objects).
     */
    public function publishAll(array $events): void;

    /**
     * Publishes domain events registered in a single entity.
     */
    public function publishEntity(object $entity, array $paramsMap = []): void;

    /**
     * Publishes all domain events from a list of entities.
     */
    public function publishEntities(array $entities, array $paramsMap = []): void;
}
