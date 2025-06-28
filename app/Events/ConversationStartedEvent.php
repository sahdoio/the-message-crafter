<?php

declare(strict_types=1);

namespace App\Events;

use Domain\Contact\Events\ConversationStarted;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationStartedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ConversationStarted $conversationStarted) {}
}
