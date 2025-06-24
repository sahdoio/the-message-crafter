<?php

declare(strict_types=1);

namespace App\Facades;

use Domain\Shared\Events\DomainEvent;
use Domain\Shared\Events\IDomainEventBus;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void publish(DomainEvent $event)
 * @method static void publishAll(array $events)
 */
class DomainEventBus extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return IDomainEventBus::class;
    }
}
