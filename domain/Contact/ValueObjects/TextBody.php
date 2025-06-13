<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects;

use Domain\Shared\ValueObjects\ValueObject;

class TextBody extends ValueObject
{
    public function __construct(
        public string $body,
        public bool $previewUrl = false,
    ) {}
}
