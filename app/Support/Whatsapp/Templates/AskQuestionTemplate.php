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
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TextBody;
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

        // persist all course options as buttons to track replies
        foreach (Course::cases() as $course) {
            Repository::for(MessageButton::class)->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $course->value,
            ]);
        }

        return new MessageBody(
            type: 'text',
            to: $contact->phone,
            text: new TextBody(
                body: 'What course would you like to enroll in?',
                previewUrl: false
            )
        );
    }
}
