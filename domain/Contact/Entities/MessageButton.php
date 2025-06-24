<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

class MessageButton
{
    public function __construct(
        public string $id,
        public string $buttonId,
        public string $messageId,
        public ?string $type = 'reply',
        public ?string $action = null
    ) {}
}
