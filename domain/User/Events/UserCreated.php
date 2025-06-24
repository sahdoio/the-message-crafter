<?php

declare(strict_types=1);

namespace Domain\User\Events;

use Domain\Shared\Events\DomainEvent;

readonly class UserCreated extends DomainEvent
{
    public function __construct()
    {
        parent::__construct('UserCreated');
    }
}
