<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\DomainEvent;

readonly class ButtonClicked extends DomainEvent
{
    public function __construct(
        public string $messageId,
        public string $contactPhone,
        public string $buttonId,
        public string $replyAction,
        public array $extraInfo = [],
    ) {
        parent::__construct('ButtonClicked');
    }
}
