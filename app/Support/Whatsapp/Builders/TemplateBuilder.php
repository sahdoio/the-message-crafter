<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Builders;

use Domain\Contact\Entities\MessageButton;

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
                'payload' => $button->buttonId,
            ]],
        ];
    }
}
