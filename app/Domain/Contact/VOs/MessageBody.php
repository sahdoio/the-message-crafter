<?php

declare(strict_types=1);

namespace App\Domain\Contact\VOs;

use App\Domain\Shared\VOs\ValueObject;

class MessageBody extends ValueObject
{
    public function __construct(
        public string $type,
        public string $to,
        public TemplateBody|null $template = null,
        public TextBody|null $text = null,
        public string|null $messaging_product = 'whatsapp',
    ) {}

    public function values(): array
    {
        $base = [
            'messaging_product' => 'whatsapp',
            'to' => $this->to,
            'type' => $this->type,
        ];

        if ($this->type === 'template' && $this->template) {
            $base['template'] = $this->template->toArray();
        }

        if ($this->type === 'text' && $this->text) {
            $base['text'] = $this->text->toArray();
        }

        return $base;
    }
}
