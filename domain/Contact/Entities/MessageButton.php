<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

class MessageButton
{
    public int $id;
    public string $buttonId;
    public string $messageId;
    public ?string $type = 'reply';
    public ?string $action = null;

    private function __construct() {}

    public static function create(
        int $id,
        string $buttonId,
        string $messageId,
        ?string $type = 'reply',
        ?string $action = null
    ): self {
        $button = new self();
        $button->id = $id;
        $button->buttonId = $buttonId;
        $button->messageId = $messageId;
        $button->type = $type;
        $button->action = $action;

        return $button;
    }
}
