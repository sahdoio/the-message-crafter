<?php

declare(strict_types=1);

namespace Domain\Messaging\Entities;

use DateTime;
use Domain\Messaging\Enums\ConversationStatus;
use Domain\Messaging\Events\ConversationStarted;
use Domain\Messaging\Events\MessageReceived;
use Domain\Messaging\Exceptions\ConversationAlreadyStartedException;
use Domain\Messaging\Repositories\IConversationRepository;
use Domain\Shared\Events\HasDomainEvents;

class Contact
{
    use HasDomainEvents;

    public ?int $id = null;
    public string $name;
    public string $email;
    public ?string $phone = null;
    public bool $verified = false;

    private IConversationRepository $conversationRepository;

    private function __construct() {}

    public static function create(
        string  $name,
        string  $email,
        ?string $phone = null,
        bool    $verified = false
    ): self
    {
        $contact = new self();
        $contact->name = $name;
        $contact->email = $email;
        $contact->phone = $phone;
        $contact->verified = $verified;

        return $contact;
    }

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

    public function messageReceived(
        int    $conversationId,
        int    $messageId,
        string $replyAction,
        ?string $buttonId = null,
        array  $extraInfo = [],
    ): void
    {
        $this->recordDomainEvent(
            new MessageReceived(
                conversationId: $conversationId,
                messageId: $messageId,
                contactPhone: $this->phone,
                replyAction: $replyAction,
                buttonId: $buttonId,
                extraInfo: $extraInfo
            )
        );
    }
}
