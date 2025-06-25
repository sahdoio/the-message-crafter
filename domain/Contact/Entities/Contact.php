<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Events\ButtonClicked;
use Domain\Contact\Events\ConversationStarted;
use Domain\Contact\Exceptions\ConversationAlreadyStartedException;
use Domain\Contact\Repositories\IConversationRepository;
use Domain\Shared\Events\HasDomainEvents;

class Contact
{
    use HasDomainEvents;

    public function __construct(
        public ?int    $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        private ?bool  $verified = true
    )
    {
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function hasName(): bool
    {
        return trim($this->name) !== '';
    }

    public function isExpecting(string $key): bool
    {
        // implement based on flow state (e.g. from db or session)
        return false;
    }

    public function hasActiveConversation(IConversationRepository $conversationRepository): bool
    {
        return $conversationRepository->hasActiveFor($this->id);
    }

    /**
     * @throws ConversationAlreadyStartedException
     */
    public function startConversation(): Conversation
    {
        if ($this->hasActiveConversation()) {
            throw new ConversationAlreadyStartedException('Only one active conversation allowed');
        }

        $conversation = new Conversation(
            contactId: $this->id,
            status: ConversationStatus::ACTIVE->value
        );

        $this->recordDomainEvent(fn(Conversation $persistedConversation) => new ConversationStarted(
            conversationId: $persistedConversation->id,
            contactId: $this->id,
        ));

        return $conversation;
    }

    public function buttonClicked(
        string $messageId,
        string $buttonId,
        string $replyAction,
        array  $extraInfo = [],
    ): void
    {
        $this->recordDomainEvent(
            new ButtonClicked(
                messageId: $messageId,
                contactPhone: $this->phone,
                buttonId: $buttonId,
                replyAction: $replyAction,
                extraInfo: $extraInfo
            )
        );
    }
}
