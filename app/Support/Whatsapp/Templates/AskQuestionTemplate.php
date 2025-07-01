<?php

declare(strict_types=1);

namespace App\Support\Whatsapp\Templates;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use App\Support\Whatsapp\Builders\TemplateBuilder;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\MessageButtonType;
use Domain\Contact\ValueObjects\Body\InteractiveListBody;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Shared\Enums\Course;
use Ramsey\Uuid\Uuid;

class AskQuestionTemplate extends TemplateBuilder
{
    /**
     * @throws ResourceNotFoundException
     */
    public function build(Conversation $conversation, Message $message): MessageBody
    {
        /** @var Contact|null $contact */
        $contact = Repository::for(Contact::class)->findById($conversation->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        $rows = [];
        foreach (Course::cases() as $course) {
            $uuid = Uuid::uuid7()->toString();

            Repository::for(MessageButton::class)->create([
                'button_id' => $uuid,
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $course->value,
            ]);

            $rows[] = [
                'id' => $uuid,
                'title' => $course->value,
            ];
        }

        $body = new InteractiveListBody(
            bodyText: 'Choose your favorite course:',
            buttonText: 'View Courses',
            sections: [
                [
                    'title' => 'Courses',
                    'rows' => $rows,
                ],
            ],
            footer: 'Pick one to continue.'
        );

        return new MessageBody(
            type: 'interactive',
            to: $contact->phone,
            body: $body
        );
    }
}
