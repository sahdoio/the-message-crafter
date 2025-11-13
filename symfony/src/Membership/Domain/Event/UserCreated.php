<?php

declare(strict_types=1);

namespace Membership\Domain\Event;

use Shared\Domain\Event\DomainEvent;

final readonly class UserCreated extends DomainEvent
{
    public function __construct(
        private ?int $userId
    ) {
        parent::__construct();
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    public function eventName(): string
    {
        return 'membership.user.created';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'user_id' => $this->userId,
        ]);
    }
}
