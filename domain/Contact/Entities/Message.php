<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTimeImmutable;
use Domain\Contact\Enums\MessageChannel;
use Domain\Contact\Enums\MessageProvider;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Enums\MessageType;

class Message
{
    public ?int $id = null;
    public int $conversationId;
    public ?string $provider = MessageProvider::SYSTEM->value;
    public ?string $channel = MessageChannel::WHATSAPP->value;
    public ?string $messageType = MessageType::TEXT->value;
    public ?string $imageUrl = null;
    public ?string $messageId = null;
    public ?array $payload = [];
    public ?string $relatedType = null;
    public ?int $relatedId = null;
    public ?string $status = MessageStatus::SENT->value;
    public ?string $sentAt = null;
    public ?int $selectedButtonId = null;

    /** @var MessageButton[] */
    public array $buttons = [];

    private function __construct() {}

    public static function create(
        int $conversationId,
        ?string $provider = MessageProvider::SYSTEM->value,
        ?string $channel = MessageChannel::WHATSAPP->value,
        ?string $messageType = MessageType::TEXT->value,
        ?string $imageUrl = null,
        ?string $messageId = null,
        ?array $payload = [],
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?string $status = MessageStatus::SENT->value,
        ?string $sentAt = null,
        ?bool $buttonSelected = null,
        array $buttons = [],
    ): self {
        $message = new self();
        $message->conversationId = $conversationId;
        $message->provider = $provider;
        $message->channel = $channel;
        $message->messageType = $messageType;
        $message->imageUrl = $imageUrl;
        $message->messageId = $messageId;
        $message->payload = $payload;
        $message->relatedType = $relatedType;
        $message->relatedId = $relatedId;
        $message->status = $status;
        $message->sentAt = $sentAt;
        $message->selectedButtonId = $buttonSelected;
        $message->buttons = $buttons;

        return $message;
    }

    public function isSent(): bool
    {
        return $this->status === 'sent' || $this->sentAt !== null;
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->sentAt = new DateTimeImmutable()->format('Y-m-d H:i:s');
    }
}
