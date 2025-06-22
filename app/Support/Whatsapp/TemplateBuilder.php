<?php

declare(strict_types=1);

namespace App\Support\Whatsapp;

use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\MessageButton;

class TemplateBuilder
{
    protected string $templateName;

    protected function generateHeaderComponent(string $imageUrl): array
    {
        return [
            'type' => 'header',
            'parameters' => [[
                'type' => 'image',
                'image' => ['link' => $imageUrl],
            ]],
        ];
    }

    protected function generateBodyComponent(Contact $contact): array
    {
        return [
            'type' => 'body',
            'parameters' => [[
                'type' => 'text',
                'text' => $contact->name,
            ]],
        ];
    }

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
