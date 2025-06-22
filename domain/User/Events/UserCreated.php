<?php

declare(strict_types=1);

namespace Domain\User\Events;

use Domain\Shared\Events\BaseEvent;

readonly class UserCreated extends BaseEvent
{
    public function __construct()
    {
        parent::__construct('UserCreated');
    }
}
