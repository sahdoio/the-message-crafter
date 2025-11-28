<?php

declare(strict_types=1);

namespace Messaging\Domain\ValueObject\Body;

interface BodyPayload
{
    public function values(): array;
}
