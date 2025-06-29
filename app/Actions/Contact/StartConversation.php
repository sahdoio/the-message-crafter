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
use Datetime;

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

        $contact->setDependencies(conversationRepository: $this->conversationRepository);

        $conversation = $contact->startConversation();

        $message = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
        ]);

        $whatsappPayload = $this->template->build($conversation, $message);

        $this->messageRepository->update($message->id, [
            'payload' => $whatsappPayload->values(),
        ]);

        DomainEventBus::publishEntity($contact);
    }
}
