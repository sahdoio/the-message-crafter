<?php

declare(strict_types=1);

namespace Messaging\Domain\ValueObject\Body;

final class TemplateBody implements BodyPayload
{
    public function __construct(
        public readonly string $name,
        public readonly string $languageCode,
        public readonly array $components = []
    ) {
    }

    public function values(): array
    {
        return [
            'name' => $this->name,
            'language' => ['code' => $this->languageCode],
            'components' => $this->components,
        ];
    }
}
