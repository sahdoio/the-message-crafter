<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class MessageFlowInputDTO extends DataTransfer
{
    public function __construct(
        public string $contactPhone,
        public string $buttonId,
        public string $replyAction,
        public array $extraInfo
    ) {}
}
