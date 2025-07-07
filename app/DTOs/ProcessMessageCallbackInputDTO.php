<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class ProcessMessageCallbackInputDTO extends DataTransfer
{
    public function __construct(
        public ?string $messageId = null,
        public ?string $recipientId = null,
        public ?array $buttonReply = [],
        public ?string $text = null,
        public ?array $errors = []
    )  {}
}
