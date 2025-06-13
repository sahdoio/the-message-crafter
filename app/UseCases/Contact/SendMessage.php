<?php

declare(strict_types=1);

namespace App\UseCases\Contact;

use App\Facades\Messenger;
use App\Facades\Repository;
use App\Models\Contact;
use App\Models\Message;
use App\Support\WhatsappTemplateBuilder;
use Domain\Contact\Enums\MessageStatus;
use Domain\Shared\Exceptions\ResourceNotFoundException;

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
