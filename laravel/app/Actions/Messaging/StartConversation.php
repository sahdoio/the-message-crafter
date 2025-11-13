<?php

declare(strict_types=1);

namespace App\Actions\Messaging;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\DomainEventBus;
use App\Support\Whatsapp\Templates\StartConversationTemplate;
use Datetime;
use Domain\Messaging\Enums\MessageStatus;
use Domain\Messaging\Repositories\IContactRepository;
use Domain\Messaging\Repositories\IConversationRepository;
use Domain\Messaging\Repositories\IMessageRepository;

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

        $contact->setDependencies(conversationRepository: $this->conversationRepository);

        $conversation = $contact->startConversation();

        $message = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
            'conversation_step' => self::class
        ]);

        $whatsappPayload = $this->template->build($conversation, $message);

        $this->messageRepository->update($message->id, [
            'payload' => $whatsappPayload->values(),
            'image_url' => $this->template->imageUrl(),
        ]);

        DomainEventBus::publishEntity($contact);
    }
}
