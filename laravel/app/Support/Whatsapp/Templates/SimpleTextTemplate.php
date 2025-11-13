<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use Domain\Messaging\Entities\Contact;
use Domain\Messaging\Entities\Conversation;
use Domain\Messaging\ValueObjects\Body\TextBody;
use Domain\Messaging\ValueObjects\MessageBody;

class SimpleTextTemplate
{
    protected string $text {
        set {
            $this->text = $value;
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function build(Conversation $conversation, ?string $text = null): MessageBody
    {
        if (!is_null($text)) {
            $this->text = $text;
        }

        /** @var Contact|null $contact */
        $contact = Repository::for(Contact::class)->findById($conversation->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: 'text',
            to: $contact->phone,
            body: new TextBody(
                body: $this->text,
                previewUrl: false
            )
        );
    }
}
