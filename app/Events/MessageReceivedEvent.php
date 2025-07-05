<?php

declare(strict_types=1);

namespace App\Events;

use Domain\Contact\Events\MessageReceived;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceivedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly MessageReceived $messageReceived) {}
}
