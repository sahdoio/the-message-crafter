<?php

declare(strict_types=1);

namespace Domain\Contact\Entities\Data;

class MessageData
{
    public function __construct(
        public int  $contactId,
        public string  $provider,
        public string  $channel,
        public string  $messageType,
        public ?string $imageUrl = null,
        public ?string $messageId = null,
        public array   $payload = [],
        public string  $status = 'pending',
        public ?string $relatedType = null,
        public ?int    $relatedId = null,
    ) {}
}

