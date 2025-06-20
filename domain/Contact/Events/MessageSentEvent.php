<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\BaseEvent;

readonly class MessageSentEvent extends BaseEvent
{
    public function __construct(
        public int   $messageId,
        public int   $contactId,
        public array $content,
    )
    {
        parent::__construct('MessageSentEvent');
    }
}
