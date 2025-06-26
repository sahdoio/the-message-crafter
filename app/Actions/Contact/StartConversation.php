<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\DomainEventBus;
use App\Support\Whatsapp\Templates\StartConversationTemplate;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Repositories\IContactRepository;
use Domain\Contact\Repositories\IConversationRepository;
use Domain\Contact\Repositories\IMessageRepository;

class StartConversation
{
    public function __construct(
        protected IContactRepository        $contactRepository,
        protected IMessageRepository        $messageRepository,
        protected IConversationRepository   $conversationRepository,
        protected StartConversationTemplate $template
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(string $to): void
    {
        $contact = $this->contactRepository->findOne(['phone' => $to]);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        $conversation = $contact->startConversation($this->conversationRepository);

        $conversation = $this->conversationRepository->create([
            'contact_id' => $conversation->contactId,
            'status' => $conversation->status
        ]);

        $message = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'status' => MessageStatus::SENT->value
        ]);

        $whatsappPayload = $this->template->build($message);

        $this->messageRepository->update($message->id, [
            'payload' => $whatsappPayload->values(),
        ]);

        DomainEventBus::publishAll();
    }
}
