<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects\Body;

class TextBody extends BodyPayload
{
    public function __construct(
        public string $body,
        public bool   $previewUrl = false,
    ) {}

    public function values(): array
    {
        return [
            'body' => $this->body,
            'preview_url' => $this->previewUrl,
        ];
    }
}
