<?php

declare(strict_types=1);

namespace App\Domain\Contact\DTOs;

use App\Domain\Shared\DTOs\DataTransfer;

readonly class SendMessageInputDTO extends DataTransfer
{
    public function __construct(
        public string $message,
        public string $type
    ) {}
}
