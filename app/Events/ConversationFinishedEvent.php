<?php

declare(strict_types=1);

namespace App\Events;

use Domain\Contact\Events\ConversationFinished;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationFinishedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ConversationFinished $conversationFinished) {}
}
