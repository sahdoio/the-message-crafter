<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Events\ButtonClicked;
use Domain\Contact\Events\ConversationStarted;
use Domain\Shared\Support\HasDomainEvents;

class Contact
{
    use HasDomainEvents;

    private function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $phone,
    ) {}

    public function startConversation(): Message
    {
        $message = new Message(
            contactId: $this->id,
            status: MessageStatus::SENT->value
        );

        $this->domainEvents[] = fn(Message $persistedMessage) => (
            new ConversationStarted(
                messageId: $persistedMessage->id,
                contactId: $this->id,
            )
        );

        return $message;
    }

    public function buttonClicked(
        string $messageId,
        string $buttonId,
        string $replyAction,
        array $extraInfo = [],
    ): void {
        $this->domainEvents[] = fn() => new ButtonClicked(
            messageId: $messageId,
            contactPhone: $this->phone,
            buttonId: $buttonId,
            replyAction: $replyAction,
            extraInfo: $extraInfo
        );
    }
}
