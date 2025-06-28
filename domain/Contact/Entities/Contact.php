<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Events\ButtonClicked;
use Domain\Contact\Events\ConversationStarted;
use Domain\Contact\Exceptions\ConversationAlreadyStartedException;
use Domain\Contact\Repositories\IConversationRepository;
use Domain\Shared\Events\HasDomainEvents;
use DateTime;

class Contact
{
    use HasDomainEvents;

    private IConversationRepository $conversationRepository;

    public function __construct(
        public ?int    $id = null,
        public string  $name,
        public string  $email,
        public ?string $phone = null,
        private bool   $verified = false
    ) {}

    public function setDependencies(IConversationRepository $conversationRepository): void
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function hasName(): bool
    {
        return trim($this->name) !== '';
    }

    public function hasActiveConversation(): bool
    {
        return $this->conversationRepository->hasActiveFor($this->id);
    }

    /**
     * @throws ConversationAlreadyStartedException
     */
    public function startConversation(): Conversation
    {
        if ($this->hasActiveConversation()) {
            throw new ConversationAlreadyStartedException(
                'Contact already has an active conversation'
            );
        }

        $conversation = $this->conversationRepository->create([
            'contact_id' => $this->id,
            'status' => ConversationStatus::ACTIVE->value,
            'started_at' => new DateTime()->format('Y-m-d H:i:s'),
        ]);

        $this->recordDomainEvent(new ConversationStarted(
            conversationId: $conversation->id,
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
