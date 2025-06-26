<?php

declare(strict_types=1);

namespace App\Support\Events;

use Domain\Shared\Events\DomainEvent;
use Domain\Shared\Events\IDomainEventBus;
use Illuminate\Support\Facades\Event;
use BadMethodCallException;

class LaravelEventBus implements IDomainEventBus
{
    protected array $map;

    public function __construct(array $eventMap = [])
    {
        $this->map = $eventMap;
    }

    public function publish(object $entity): void
    {
        if (!method_exists($entity, 'releaseDomainEvents')) {
            throw new BadMethodCallException(
                sprintf('Entity %s must implement releaseDomainEvents method.', get_class($entity))
            );
        }

        foreach ($entity->releaseDomainEvents() as $eventFactory) {
            $event = $eventFactory(); // invoke closure or direct event

            $laravelClass = $this->map[get_class($event)] ?? null;

            if ($laravelClass) {
                $wrappedEvent = new $laravelClass($event);
                Event::dispatch($wrappedEvent);
            } else {
                Event::dispatch($event);
            }
        }
    }

    public function publishAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->publish($entity);
        }
    }
}
