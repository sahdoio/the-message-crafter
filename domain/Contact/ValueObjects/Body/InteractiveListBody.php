<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects\Body;

class InteractiveListBody extends BodyPayload
{
    /**
     * @param string $bodyText main message body text
     * @param string $buttonText text that shows on the list-opening button (e.g. "View Options")
     * @param array $sections list of sections, each being: ['title' => string, 'rows' => [['id' => string, 'title' => string, 'description' => ?string]]]
     * @param string|null $footer optional footer text
     */
    public function __construct(
        public string  $bodyText,
        public string  $buttonText,
        public array   $sections,
        public ?string $footer = null,
    ) {}

    public function values(): array
    {
        return array_filter([
            'type' => 'list',
            'body' => [
                'text' => $this->bodyText,
            ],
            'footer' => $this->footer ? ['text' => $this->footer] : null,
            'action' => [
                'button' => $this->buttonText,
                'sections' => array_map(
                    fn(array $section) => [
                        'title' => $section['title'],
                        'rows' => array_map(
                            fn(array $row) => array_filter([
                                'id' => $row['id'],
                                'title' => $row['title'],
                                'description' => $row['description'] ?? null,
                            ]),
                            $section['rows']
                        ),
                    ],
                    $this->sections
                ),
            ],
        ]);
    }
}
