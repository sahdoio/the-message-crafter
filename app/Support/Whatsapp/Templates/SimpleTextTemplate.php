<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TextBody;

class SimpleTextTemplate
{
    /**
     * @throws ResourceNotFoundException
     */
    public function build(Conversation $conversation, string $text): MessageBody
    {
        /** @var Contact|null $contact */
        $contact = Repository::for(Contact::class)->findById($conversation->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: 'text',
            to: $contact->phone,
            text: new TextBody(
                body: $text,
                previewUrl: false
            )
        );
    }
}
