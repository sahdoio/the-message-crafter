<?php

declare(strict_types=1);

namespace Domain\Messaging\ValueObjects;

use Domain\Messaging\ValueObjects\Body\BodyPayload;
use Domain\Messaging\ValueObjects\Body\InteractiveButtonsBody;
use Domain\Messaging\ValueObjects\Body\InteractiveListBody;
use Domain\Messaging\ValueObjects\Body\TemplateBody;
use Domain\Messaging\ValueObjects\Body\TextBody;
use Domain\Shared\ValueObjects\ValueObject;
use InvalidArgumentException;

class MessageBody extends ValueObject
{
    public function __construct(
        public string           $type,
        public string           $to,
        public BodyPayload|null $body = null,
        public string           $messaging_product = 'whatsapp',
    )
    {
        if (!in_array($type, ['template', 'text', 'interactive'])) {
            throw new InvalidArgumentException("Invalid message type: $type");
        }
    }

    public function values(): array
    {
        $base = [
            'messaging_product' => $this->messaging_product,
            'to' => $this->to,
            'type' => $this->type,
        ];

        if ($this->body instanceof TemplateBody) {
            $base['template'] = $this->body->values();
        }

        if ($this->body instanceof TextBody) {
            $base['text'] = $this->body->values();
        }

        if (
            $this->body instanceof InteractiveButtonsBody ||
            $this->body instanceof InteractiveListBody
        ) {
            $base['interactive'] = $this->body->values();
        }

        return $base;
    }
}
