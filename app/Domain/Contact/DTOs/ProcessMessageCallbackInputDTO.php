<?php

declare(strict_types=1);

namespace App\Domain\Contact\DTOs;

use App\Domain\Shared\DTOs\DataTransfer;

readonly class ProcessMessageCallbackInputDTO extends DataTransfer
{
    public function __construct(
        public ?string $messageId = null,
        public ?string $recipientId = null,
        public ?array $buttonReply = [],
        public ?array $errors = []
    )  {}
}
