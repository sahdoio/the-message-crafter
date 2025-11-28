<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Service\Whatsapp\Builders;

use Messaging\Domain\Entity\Contact;

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
                'text' => $contact->getName(),
            ]],
        ];
    }
}
