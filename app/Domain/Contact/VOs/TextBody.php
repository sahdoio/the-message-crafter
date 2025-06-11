<?php

declare(strict_types=1);

namespace App\Domain\Contact\VOs;

use App\Domain\Shared\VOs\ValueObject;

class TextBody extends ValueObject
{
    public function __construct(
        public string $body,
        public bool $previewUrl = false,
    ) {}
}
