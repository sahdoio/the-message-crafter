<?php

declare(strict_types=1);

namespace Messaging\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Messaging\Domain\Enum\ConversationStatus;
use Messaging\Domain\Event\ConversationFinished;
use Messaging\Domain\Exception\ConversationNotActiveException;
use Shared\Domain\Event\HasDomainEvents;

#[ORM\Entity]
#[ORM\Table(name: 'conversations')]
class Conversation
{
    use HasDomainEvents;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $contactId;

    #[ORM\Column(type: 'string', length: 20, enumType: ConversationStatus::class)]
    private ConversationStatus $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $strategyClass = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $currentStep = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $finishedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /** @var Message[] */
    private array $messages = [];

    private function __construct(int $contactId)
    {
        $this->contactId = $contactId;
        $this->status = ConversationStatus::ACTIVE;
        $this->startedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(int $contactId): self
    {
        return new self($contactId);
    }

    public function isActive(): bool
    {
        return $this->status === ConversationStatus::ACTIVE;
    }

    public function finish(): void
    {
        if (!$this->isActive()) {
            throw new ConversationNotActiveException('Conversation is not active');
        }

        $this->status = ConversationStatus::FINISHED;
        $this->finishedAt = new DateTimeImmutable();
        $this->touch();

        $this->recordDomainEvent(new ConversationFinished(
            conversationId: $this->id,
            contactId: $this->contactId,
            finishedAt: $this->finishedAt->format('Y-m-d H:i:s')
        ));
    }

    public function addMessage(Message $message): void
    {
        if (!$this->isActive()) {
            throw new ConversationNotActiveException('Cannot add message to inactive conversation');
        }

        $this->messages[] = $message;
        $this->touch();
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }

    public function startStrategy(string $strategyClass): void
    {
        $this->strategyClass = $strategyClass;
        $this->touch();
    }

    public function advanceToStep(string $step): void
    {
        $this->currentStep = $step;
        $this->touch();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function getStatus(): ConversationStatus
    {
        return $this->status;
    }

    public function getStrategyClass(): ?string
    {
        return $this->strategyClass;
    }

    public function getCurrentStep(): ?string
    {
        return $this->currentStep;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
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
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
