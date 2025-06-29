<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects;

use Domain\Shared\ValueObjects\ValueObject;

class TemplateBody extends ValueObject
{
    public function __construct(
        public string $name,
        public string $languageCode,
        public array $components = [],
    ) {}

    public function values(): array
    {
        return [
            'name' => $this->name,
            'language' => ['code' => $this->languageCode],
            'components' => $this->components,
        ];
    }
}
