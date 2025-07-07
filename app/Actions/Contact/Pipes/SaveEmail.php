<?php

declare(strict_types=1);

namespace App\Actions\Contact\Pipes;

use App\DTOs\MessageFlowInputDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\DomainEventBus;
use App\Facades\Messenger;
use App\Facades\Repository;
use App\Support\Whatsapp\Templates\AskEmailSecondTimeTemplate;
use App\Support\Whatsapp\Templates\EmailSavedSuccessfullyTemplate;
use Closure;
use DateTime;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Enums\MessageStatus;
use Illuminate\Support\Facades\Log;

class SaveEmail
{
    public const string STEP_ID = 'save_email';

    public function __construct(
        protected AskEmailSecondTimeTemplate $askEmailSecondTimeTemplate,
        protected EmailSavedSuccessfullyTemplate $askEmailSavedSuccessfullyTemplate
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageFlowInputDTO $data, Closure $next): mixed
    {
        Repository::for(Conversation::class)->update($data->conversation->id, [
            'current_step' => self::class,
        ]);

        $email = $data->replyAction ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::info('SaveEmail - Invalid email provided', [
                'conversation_id' => $data->conversation->id,
                'email_attempt' => $email,
            ]);

            Messenger::send(Repository::for(Message::class)->create([
                'conversation_id' => $data->conversation->id,
                'status' => MessageStatus::SENT->value,
                'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
                'payload' => $this->askEmailSecondTimeTemplate->build($data->conversation)->values(),
            ]));

            return null;
        }

        Repository::for(Message::class)->update($data->messageId, [
            'reply_text' => $email,
        ]);

        Messenger::send(Repository::for(Message::class)->create([
            'conversation_id' => $data->conversation->id,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
            'payload' => $this->askEmailSavedSuccessfullyTemplate->build($data->conversation)->values(),
        ]));

        $data->conversation->finish();
        Repository::for(Conversation::class)->persistEntity($data->conversation);
        DomainEventBus::publishEntity($data->conversation);

        Log::info('SaveEmail - Email saved', [
            'conversation_id' => $data->conversation->id,
            'email' => $email,
            'step' => self::STEP_ID,
        ]);

        return null;
    }
}
