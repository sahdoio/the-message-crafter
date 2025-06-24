<?php

declare(strict_types=1);

namespace App\Support\Events;

use Domain\Shared\Events\DomainEvent;
use Domain\Shared\Events\IDomainEventBus;
use Illuminate\Support\Facades\Event;

class LaravelEventBus implements IDomainEventBus
{
    protected array $map;

    public function __construct(array $eventMap = [])
    {
        $this->map = $eventMap;
    }

    public function publish(DomainEvent $event): void
    {
        $laravelClass = $this->map[get_class($event)] ?? null;

        if ($laravelClass) {
            $wrappedEvent = new $laravelClass($event);
            Event::dispatch($wrappedEvent);
        } else {
            // optionally fallback to dispatching the domain event directly
            Event::dispatch($event);
        }
    }

    public function publishAll(array $events): void
    {
        foreach ($events as $event) {
            $this->publish($event);
        }
    }
}

