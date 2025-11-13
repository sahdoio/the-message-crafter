<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Service\Event;

use Shared\Domain\Event\DomainEvent;
use Shared\Domain\Service\DomainEventBusInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyDomainEventBus implements DomainEventBusInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function publish(DomainEvent $event): void
    {
        $this->eventDispatcher->dispatch($event, $event->eventName());
    }

    public function publishAll(array $events): void
    {
        foreach ($events as $event) {
            $this->publish($event);
        }
    }

    public function publishEntity(object $entity): void
    {
        if (!method_exists($entity, 'releaseDomainEvents')) {
            return;
        }

        $events = $entity->releaseDomainEvents();
        $this->publishAll($events);
    }

    public function publishEntities(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->publishEntity($entity);
        }
    }
}
