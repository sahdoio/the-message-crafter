<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\BaseEvent;

readonly class StrategySelected extends BaseEvent
{
    public function __construct()
    {
        parent::__construct('StrategySelected');
    }
}
