<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTimeImmutable;
use Domain\Contact\Entities\Data\MessageData;

class Message
{
    public int $id;
    public int $contactId;
    public string $provider; // 'flow', 'ai'
    public string $channel; // 'whatsapp', 'email'
    public string $messageType; // 'text', 'template'
    public ?string $imageUrl = null;
    public ?string $messageId = null;

    /** @var array<string, mixed> */
    public array $payload = [];

    public string $status = 'pending'; // 'pending', 'sent', 'delivered', 'failed'
    public ?DateTimeImmutable $sentAt = null;

    public ?string $relatedType = null;
    public ?int $relatedId = null;

    public DateTimeImmutable $createdAt;
    public DateTimeImmutable $updatedAt;

    /**
     * @var MessageButton[]
     */
    public array $buttons = [];

    private function __construct() {}

    public static function create(MessageData $data): self
    {
        $message = new self();

        $message->contactId = $data->contactId;
        $message->provider = $data->provider;
        $message->channel = $data->channel;
        $message->messageType = $data->messageType;
        $message->imageUrl = $data->imageUrl;
        $message->messageId = $data->messageId;
        $message->payload = $data->payload;
        $message->status = $data->status;
        $message->sentAt = null;
        $message->relatedType = $data->relatedType;
        $message->relatedId = $data->relatedId;

        $message->createdAt = new DateTimeImmutable();
        $message->updatedAt = new DateTimeImmutable();

        if (isset($data->payload['buttons']) && is_array($data->payload['buttons'])) {
            $message->buttons = array_map(
                fn(array $btn) => MessageButton::create($btn),
                $data->payload['buttons']
            );
        }

        return $message;
    }

    // ✅ behavior
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
