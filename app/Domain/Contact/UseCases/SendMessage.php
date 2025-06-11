<?php

declare(strict_types=1);

namespace App\Domain\Contact\UseCases;

use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\Entities\Message;
use App\Domain\Contact\Enums\MessageStatus;
use App\Domain\Contact\Support\WhatsappTemplateBuilder;
use App\Domain\Shared\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Facades\Repository;

class SendMessage
{
    private WhatsappTemplateBuilder $templateBuilder;

    public function __construct()
    {
        $templateName = config('whatsapp.template_name');
        $this->templateBuilder = new WhatsappTemplateBuilder($templateName);
    }

    /**
     * Send a WhatsApp message and persist it.
     *
     * @throws ResourceNotFoundException
     */
    public function handle(string $to): bool
    {
        $contact = Repository::for(Contact::class)->findOne(['phone' => $to]);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        $repo = Repository::for(Message::class);

        $message = $repo->create([
            'contact_id' => $contact->id,
            'status' => MessageStatus::PENDING->value
        ]);

        $whatsappPayload = $this->templateBuilder->build($message);

        $repo->update($message->id, [
            'payload' => $whatsappPayload->values()
        ]);

        return Messenger::send($whatsappPayload);
    }
}
