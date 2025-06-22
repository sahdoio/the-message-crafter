<?php

declare(strict_types=1);

namespace App\Support\Whatsapp;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\MessageButtonType;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TemplateBody;
use Ramsey\Uuid\Uuid;

class StartConversationTemplate extends TemplateBuilder
{
    public function __construct()
    {
        $this->templateName = config('whatsapp.template_name');
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function build(Message $message): MessageBody
    {
        $imageUrl = config('whatsapp.template_image_url');

        $buttons = [
            'Start Course',
            'Dive Deeper',
            'Help or Support',
        ];

        $buttonComponents = [];
        foreach ($buttons as $index => $action) {
            $button = Repository::for(MessageButton::class)->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $action,
            ]);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        $contact = Repository::for(Contact::class)->findById($message->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: 'template',
            to: $contact->phone,
            template: new TemplateBody(
                name: $this->templateName,
                languageCode: 'pt_BR',
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
