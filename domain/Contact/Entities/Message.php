<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTimeImmutable;
use Domain\Contact\Enums\MessageChannel;
use Domain\Contact\Enums\MessageProvider;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Enums\MessageType;
use Domain\Contact\ValueObjects\MessageBody;

class Message
{
    public function __construct(
        public ?int $id = null,
        public ?int $contactId = null,
        public ?string $provider = MessageProvider::SYSTEM->value,
        public ?string $channel = MessageChannel::WHATSAPP->value,
        public ?string $messageType = MessageType::TEXT->value,
        public ?string $imageUrl = null,
        public ?string $messageId = null,
        public ?MessageBody $body = null,
        public ?string $relatedType = null,
        public ?int $relatedId = null,
        public ?string $status = MessageStatus::PENDING->value,
        public ?DateTimeImmutable $sentAt = null,
        /**
         * @var MessageButton[]
         */
        public array $buttons = []
    ) {}

    public function isSent(): bool
    {
        return $this->status === 'sent' || $this->sentAt !== null;
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->sentAt = new DateTimeImmutable();
    }
}
