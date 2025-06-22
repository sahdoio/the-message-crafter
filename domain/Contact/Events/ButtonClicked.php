<?php

declare(strict_types=1);

namespace Domain\Contact\Events;

use Domain\Shared\Events\BaseEvent;

readonly class ButtonClicked extends BaseEvent
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
