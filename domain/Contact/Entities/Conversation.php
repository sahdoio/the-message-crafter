<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use DateTime;
use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Events\ConversationFinished;
use Domain\Shared\Attributes\SkipPersistence;
use Domain\Shared\Events\HasDomainEvents;
use DomainException;

class Conversation
{
    use HasDomainEvents;

    public ?int $id = null;
    public int $contactId;
    public string $status = ConversationStatus::ACTIVE->value;
    public ?string $startedAt = null;
    public ?string $finishedAt = null;
    public ?string $strategyClass = null;
    public ?string $currentStep = null;

    /** @var Message[] */
    #[SkipPersistence]
    public array $messages = [];

    private function __construct()
    {
    }

    public static function create(
        int     $contactId,
        ?string $status = ConversationStatus::ACTIVE->value,
        ?string $startedAt = null,
        ?string $finishedAt = null,
        ?string $strategyClass = null,
        ?string $currentStep = null,
        array   $messages = [],
    ): self
    {
        $conversation = new self();
        $conversation->contactId = $contactId;
        $conversation->status = $status ?? ConversationStatus::ACTIVE->value;
        $conversation->startedAt = $startedAt ?? new DateTime()->format('Y-m-d H:i:s');
        $conversation->finishedAt = $finishedAt;
        $conversation->strategyClass = $strategyClass;
        $conversation->currentStep = $currentStep;
        $conversation->messages = $messages;

        return $conversation;
    }

    public function isActive(): bool
    {
        return $this->status === ConversationStatus::ACTIVE->value;
    }

    public function finish(): void
    {
        if (!$this->isActive()) {
            throw new DomainException("Cannot end a conversation that is not active.");
        }

        $this->status = ConversationStatus::FINISHED->value;
        $this->finishedAt = new DateTime()->format('Y-m-d H:i:s');

        $this->recordDomainEvent(new ConversationFinished(
            conversationId: $this->id,
            contactId: $this->contactId,
            finishedAt: $this->finishedAt
        ));
    }

    public function addMessage(Message $message): void
    {
        if (!$this->isActive()) {
            throw new DomainException("Cannot add message to closed conversation.");
        }

        $this->messages[] = $message;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }

    public function startStrategy(string $strategyClass): void
    {
        $this->strategyClass = $strategyClass;
        $this->currentStep = null;
    }

    public function advanceToStep(string $step): void
    {
        $this->currentStep = $step;
    }
}
