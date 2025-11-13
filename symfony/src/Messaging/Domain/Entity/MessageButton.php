<?php

declare(strict_types=1);

namespace Messaging\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Messaging\Domain\Enum\MessageButtonType;

#[ORM\Entity]
#[ORM\Table(name: 'message_buttons')]
class MessageButton
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $buttonId;

    #[ORM\Column(type: 'integer')]
    private int $messageId;

    #[ORM\Column(type: 'string', length: 20, enumType: MessageButtonType::class)]
    private MessageButtonType $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    private function __construct(
        string $buttonId,
        int $messageId,
        MessageButtonType $type = MessageButtonType::REPLY,
        ?string $action = null
    ) {
        $this->buttonId = $buttonId;
        $this->messageId = $messageId;
        $this->type = $type;
        $this->action = $action;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        string $buttonId,
        int $messageId,
        MessageButtonType $type = MessageButtonType::REPLY,
        ?string $action = null
    ): self {
        return new self($buttonId, $messageId, $type, $action);
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getButtonId(): string
    {
        return $this->buttonId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getType(): MessageButtonType
    {
        return $this->type;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
