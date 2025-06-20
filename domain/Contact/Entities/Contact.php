<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

use Domain\Contact\Entities\Data\MessageData;
use Domain\Contact\Events\MessageSentEvent;

class Contact
{
    public int $id;
    public string $name;
    public string $email;
    public array $domainEvents = [];

    private function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public static function create(): void
    {
        // TODO
    }

    public static function fromDatabase(): void
    {
        // TODO
    }

    public function sendMessage(array $content): void
    {
        $message = Message::create(new MessageData(
            contactId: $this->id,
            provider: 'flow',
            channel: 'whatsapp',
            messageType: 'template',
            payload: $content,
            status: 'pending',
        ));

        $this->domainEvents[] = fn($persistedMessage) => (
            new MessageSentEvent(
                messageId: $persistedMessage->id,
                contactId: $this->id,
                content: $content
            )
        );
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
