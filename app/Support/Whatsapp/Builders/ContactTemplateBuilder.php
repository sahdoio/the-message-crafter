<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Builders;

use Domain\Contact\Entities\Contact;

class ContactTemplateBuilder extends TemplateBuilder
{
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
}
