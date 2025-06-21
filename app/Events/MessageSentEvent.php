<?php

declare(strict_types=1);

namespace App\Events;

use Domain\Contact\Events\MessageSent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly MessageSent $domainEvent) {}
}
