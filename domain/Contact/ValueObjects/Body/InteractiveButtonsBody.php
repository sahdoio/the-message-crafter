<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects\Body;

use Domain\Shared\ValueObjects\ValueObject;

class InteractiveButtonsBody extends ValueObject
{
    public function __construct(
        public string $text,
        public array  $buttons // each item must be: ['id' => string, 'title' => string]
    ) {}

    public function values(): array
    {
        return [
            'type' => 'button',
            'body' => [
                'text' => $this->text,
            ],
            'action' => [
                'buttons' => array_map(
                    fn(array $btn) => [
                        'type' => 'reply',
                        'reply' => [
                            'id' => $btn['id'],
                            'title' => $btn['title'],
                        ]
                    ],
                    $this->buttons
                ),
            ],
        ];
    }
}
