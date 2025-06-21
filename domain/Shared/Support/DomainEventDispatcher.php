<?php

declare(strict_types=1);

namespace Domain\Shared\Support;

class DomainEventDispatcher
{
    public static function dispatchFrom(object $aggregate, object $entity): void
    {
        if (!method_exists($aggregate, 'pullDomainEvents')) {
            return;
        }

        foreach ($aggregate->pullDomainEvents() as $eventFactory) {
            $event = $eventFactory($entity);
            event($event);
        }
    }
}
