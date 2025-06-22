<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Exceptions\ResourceNotFoundException;
use App\Support\Events\DomainEventDispatcher;
use App\Support\Whatsapp\Templates\StartConversationTemplate;
use Domain\Contact\Repositories\IContactRepository;
use Domain\Contact\Repositories\IMessageRepository;

class StartConversation
{
    public function __construct(
        protected IContactRepository $contactRepository,
        protected IMessageRepository $messageRepository,
        protected StartConversationTemplate $template,
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(string $to): bool
    {
        $contact = $this->contactRepository->findOne(['phone' => $to]);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        $message = $contact->startConversation();

        $message = $this->messageRepository->create([
            'contact_id' => $message->contactId,
            'status'     => $message->status
        ]);

        $whatsappPayload = $this->template->build($message);

        $message = $this->messageRepository->update($message->id, [
            'payload' => $whatsappPayload->values(),
        ]);

        DomainEventDispatcher::dispatch($contact, $message);

        return true;
    }
}
