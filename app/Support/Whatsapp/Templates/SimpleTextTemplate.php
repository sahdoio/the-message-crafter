<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\ValueObjects\Body\TextBody;
use Domain\Contact\ValueObjects\MessageBody;

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
