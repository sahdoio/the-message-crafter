<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class SendMessageInputDTO extends DataTransfer
{
    public function __construct(
        public string $message,
        public string $type
    ) {}
}
