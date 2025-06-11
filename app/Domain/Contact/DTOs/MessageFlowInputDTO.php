<?php

declare(strict_types=1);

namespace App\Domain\Contact\DTOs;

use App\Domain\Shared\DTOs\DataTransfer;

readonly class MessageFlowInputDTO extends DataTransfer
{
    public function __construct(
        public string $contactPhone,
        public string $buttonId,
        public string $replyAction,
        public array $extraInfo
    ) {}
}
