<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Service\Whatsapp\Builders;

use Messaging\Domain\Entity\MessageButton;

class TemplateBuilder
{
    protected string $templateName;

    protected function generateButtonComponent(MessageButton $button, int $index): array
    {
        return [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => $index,
            'parameters' => [[
                'type' => 'payload',
                'payload' => $button->getButtonId(),
            ]],
        ];
    }
}
