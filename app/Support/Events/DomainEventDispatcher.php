<?php

declare(strict_types=1);

namespace App\Support\Events;

use BadMethodCallException;
use Illuminate\Support\Facades\Event;

class DomainEventDispatcher
{
    public static function dispatchFrom(object $entity, object $persistedData): void
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
            $domainEvent = $factory($persistedData);

            $laravelEventClass = $eventMap[get_class($domainEvent)] ?? null;

            if ($laravelEventClass !== null) {
                $laravelEvent = new $laravelEventClass($domainEvent);
                Event::dispatch($laravelEvent);
            }
        }
    }
}

