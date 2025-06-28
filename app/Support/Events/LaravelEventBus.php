<?php

declare(strict_types=1);

namespace App\Support\Events;

use Closure;
use Illuminate\Support\Facades\Event;
use BadMethodCallException;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use Domain\Shared\Events\DomainEvent;
use Domain\Shared\Events\IDomainEventBus;

class LaravelEventBus implements IDomainEventBus
{
    protected array $map;

    public function __construct(array $eventMap = [])
    {
        $this->map = $eventMap;
    }

    /**
     * Publish a single DomainEvent or Closure-based event.
     * @throws ReflectionException
     */
    public function publish(DomainEvent|Closure $event, array $paramsMap = []): void
    {
        if ($event instanceof Closure) {
            $event = $this->resolveEventCallable($event, $paramsMap);
        }

        $wrapped = $this->map[get_class($event)] ?? null;

        Event::dispatch($wrapped ? new $wrapped($event) : $event);
    }

    /**
     * Publish an array of events (DomainEvent or Closure).
     * @throws ReflectionException
     */
    public function publishAll(array $events, array $paramsMap = []): void
    {
        foreach ($events as $event) {
            $this->publish($event, $paramsMap);
        }
    }

    /**
     * Publish all domain events from a single entity.
     * @throws ReflectionException
     */
    public function publishEntity(object $entity, array $paramsMap = []): void
    {
        if (!method_exists($entity, 'releaseDomainEvents')) {
            throw new BadMethodCallException(
                sprintf('Entity %s must implement releaseDomainEvents method.', get_class($entity))
            );
        }

        $this->publishAll($entity->releaseDomainEvents(), $paramsMap);
    }

    /**
     * Publish all domain events from a list of entities.
     * @throws ReflectionException
     */
    public function publishEntities(array $entities, array $paramsMap = []): void
    {
        foreach ($entities as $entity) {
            $this->publishEntity($entity, $paramsMap);
        }
    }

    /**
     * Resolve closure-based domain events with matching arguments.
     * @throws ReflectionException
     */
    private function resolveEventCallable(Closure $eventFactory, array $paramsMap): object
    {
        $reflection = new ReflectionFunction($eventFactory);
        $returnType = $reflection->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            return $eventFactory(); // no declared return type
        }

        $eventClass = $returnType->getName();
        $args = $paramsMap[$eventClass] ?? [];

        return $eventFactory(...$args);
    }
}
