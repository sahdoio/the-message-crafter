<?php

declare(strict_types=1);

namespace App\Support\Events;

use BadMethodCallException;
use Illuminate\Support\Facades\Event;

class DomainEventDispatcher
{
    public static function dispatch(object $entity, ?object $closureEventData = null): void
    {
        if (!method_exists($entity, 'pullDomainEvents')) {
            /**
             * @link  HasDomainEvents
             */
            throw new BadMethodCallException(
                'The entity must implement the pullDomainEvents method.'
            );
        }

        $eventMap = app('domainEvent.map');

        foreach ($entity->pullDomainEvents() as $factory) {
            $domainEvent = $closureEventData ? $factory($closureEventData) : $factory();

            $laravelEventClass = $eventMap[get_class($domainEvent)] ?? null;

            if ($laravelEventClass !== null) {
                $laravelEvent = new $laravelEventClass($domainEvent);
                Event::dispatch($laravelEvent);
            }
        }
    }
}

