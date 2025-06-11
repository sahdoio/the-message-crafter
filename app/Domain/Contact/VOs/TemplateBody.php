<?php

declare(strict_types=1);

namespace App\Domain\Contact\VOs;

use App\Domain\Shared\VOs\ValueObject;

class TemplateBody extends ValueObject
{
    public function __construct(
        public string $name,
        public string $languageCode,
        public array $components = [],
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'language' => ['code' => $this->languageCode],
            'components' => $this->components,
        ];
    }
}
