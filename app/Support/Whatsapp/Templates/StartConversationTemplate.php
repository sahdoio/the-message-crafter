<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use App\Support\Whatsapp\Builders\ContactTemplateBuilder;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\MessageButtonType;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TemplateBody;
use Ramsey\Uuid\Uuid;

class StartConversationTemplate extends ContactTemplateBuilder
{
    public function __construct()
    {
        $this->templateName = config('whatsapp.template_name');
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function build(Conversation $conversation, Message $message): MessageBody
    {
        $imageUrl = config('whatsapp.template_image_url');

        $buttons = [
            'Start Course',
            'Dive Deeper',
            'Help or Support',
        ];

        $buttonComponents = [];
        foreach ($buttons as $index => $action) {
            /** @var MessageButton $button */
            $button = Repository::for(MessageButton::class)->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $action,
            ]);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        /** @var Contact $contact */
        $contact = Repository::for(Contact::class)->findById($conversation->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: config('whatsapp.message_type'),
            to: $contact->phone,
            template: new TemplateBody(
                name: $this->templateName,
                languageCode: config('whatsapp.language_code'),
                components: array_merge(
                    [
                        $this->generateHeaderComponent($imageUrl),
                        $this->generateBodyComponent($contact),
                    ],
                    $buttonComponents
                )
            )
        );
    }
}
