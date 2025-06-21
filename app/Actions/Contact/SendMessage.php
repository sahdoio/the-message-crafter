<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Support\WhatsappTemplateBuilder;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Repositories\IContactRepository;
use Domain\Contact\Repositories\IMessageRepository;
use Domain\Shared\Support\DomainEventDispatcher;

class SendMessage
{
    private WhatsappTemplateBuilder $templateBuilder;

    public function __construct(
        protected IContactRepository $contactRepository,
        protected IMessageRepository $messageRepository,
    ) {
        $templateName = config('whatsapp.template_name');
        $this->templateBuilder = new WhatsappTemplateBuilder($templateName);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(string $to): bool
    {
        $contact = $this->contactRepository->findOne(['phone' => $to]);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        $domainMessage = $contact->sendMessage([
            'to'     => $to,
            'status' => MessageStatus::PENDING->value,
        ]);

        $persistedMessage = $this->messageRepository->create([
            'contact_id' => $domainMessage->contactId,
            'status'     => $domainMessage->status,
            'payload'    => $domainMessage->payload,
        ]);

        $domainMessage->id = $persistedMessage->id;

        $whatsappPayload = $this->templateBuilder->build($persistedMessage);

        $this->messageRepository->update($persistedMessage->id, [
            'payload' => $whatsappPayload->values(),
        ]);

        DomainEventDispatcher::dispatchFrom($contact, $persistedMessage);

        return Messenger::send($whatsappPayload);
    }
}
