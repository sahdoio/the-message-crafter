<?php

declare(strict_types=1);

namespace Domain\Messaging\ValueObjects\Body;

class TemplateBody extends BodyPayload
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
