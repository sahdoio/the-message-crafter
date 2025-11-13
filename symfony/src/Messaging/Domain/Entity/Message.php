<?php

declare(strict_types=1);

namespace Messaging\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Messaging\Domain\Enum\MessageChannel;
use Messaging\Domain\Enum\MessageProvider;
use Messaging\Domain\Enum\MessageStatus;
use Messaging\Domain\Enum\MessageType;

#[ORM\Entity]
#[ORM\Table(name: 'messages')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $conversationId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $conversationStep = null;

    #[ORM\Column(type: 'string', length: 20, enumType: MessageProvider::class, nullable: true)]
    private ?MessageProvider $provider = null;

    #[ORM\Column(type: 'string', length: 20, enumType: MessageChannel::class, nullable: true)]
    private ?MessageChannel $channel = null;

    #[ORM\Column(type: 'string', length: 20, enumType: MessageType::class, nullable: true)]
    private ?MessageType $messageType = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $messageId = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $payload = null;

    #[ORM\Column(type: 'string', length: 20, enumType: MessageStatus::class)]
    private MessageStatus $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $selectedButtonId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $replyText = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /** @var MessageButton[] */
    private array $buttons = [];

    private function __construct(
        int $conversationId,
        ?MessageProvider $provider = null,
        ?MessageChannel $channel = null,
        ?MessageType $messageType = null
    ) {
        $this->conversationId = $conversationId;
        $this->provider = $provider;
        $this->channel = $channel;
        $this->messageType = $messageType;
        $this->status = MessageStatus::SENT;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        int $conversationId,
        ?MessageProvider $provider = null,
        ?MessageChannel $channel = null,
        ?MessageType $messageType = null
    ): self {
        return new self($conversationId, $provider, $channel, $messageType);
    }

    public function isSent(): bool
    {
        return $this->status === MessageStatus::SENT;
    }

    public function markAsSent(): void
    {
        $this->status = MessageStatus::SENT;
        $this->sentAt = new DateTimeImmutable();
        $this->touch();
    }

    public function markAsFinished(): void
    {
        $this->status = MessageStatus::FINISHED;
        $this->touch();
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
        $this->touch();
    }

    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
        $this->touch();
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
        $this->touch();
    }

    public function setConversationStep(string $step): void
    {
        $this->conversationStep = $step;
        $this->touch();
    }

    public function selectButton(int $buttonId): void
    {
        $this->selectedButtonId = $buttonId;
        $this->touch();
    }

    public function setReplyText(string $text): void
    {
        $this->replyText = $text;
        $this->touch();
    }

    public function addButton(MessageButton $button): void
    {
        $this->buttons[] = $button;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function getConversationStep(): ?string
    {
        return $this->conversationStep;
    }

    public function getProvider(): ?MessageProvider
    {
        return $this->provider;
    }

    public function getChannel(): ?MessageChannel
    {
        return $this->channel;
    }

    public function getMessageType(): ?MessageType
    {
        return $this->messageType;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getStatus(): MessageStatus
    {
        return $this->status;
    }

    public function getSelectedButtonId(): ?int
    {
        return $this->selectedButtonId;
    }

    public function getReplyText(): ?string
    {
        return $this->replyText;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return MessageButton[]
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
