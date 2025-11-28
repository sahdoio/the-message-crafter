<?php

declare(strict_types=1);

namespace Messaging\Domain\ValueObject;

use Messaging\Domain\ValueObject\Body\BodyPayload;
use Messaging\Domain\ValueObject\Body\TemplateBody;
use InvalidArgumentException;

final class MessageBody
{
    public function __construct(
        public readonly string $type,
        public readonly string $to,
        public readonly ?BodyPayload $body = null,
        public readonly string $messagingProduct = 'whatsapp',
    ) {
        if (!in_array($type, ['template', 'text', 'interactive'])) {
            throw new InvalidArgumentException("Invalid message type: $type");
        }
    }

    public function values(): array
    {
        $base = [
            'messaging_product' => $this->messagingProduct,
            'to' => $this->to,
            'type' => $this->type,
        ];

        if ($this->body instanceof TemplateBody) {
            $base['template'] = $this->body->values();
        }

        // Add other body types here as needed (TextBody, InteractiveBody)

        return $base;
    }
}
