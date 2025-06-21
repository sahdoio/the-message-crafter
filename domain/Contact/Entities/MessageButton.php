<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

class MessageButton
{
    public function __construct(
        public string $id,
        public string $label,
        public ?string $type = 'reply'
    ) {}
}
