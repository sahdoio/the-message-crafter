<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class SendMessageOutputDTO extends DataTransfer
{
    public function __construct(
        public string $message,
        public string $uuid
    )
    {
    }
}
