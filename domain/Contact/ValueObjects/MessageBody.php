<?php

declare(strict_types=1);

namespace Domain\Contact\ValueObjects;

use Domain\Contact\ValueObjects\Body\BodyPayload;
use Domain\Contact\ValueObjects\Body\InteractiveButtonsBody;
use Domain\Contact\ValueObjects\Body\InteractiveListBody;
use Domain\Contact\ValueObjects\Body\TemplateBody;
use Domain\Contact\ValueObjects\Body\TextBody;
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
