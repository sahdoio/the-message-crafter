<?php

declare(strict_types=1);

namespace App\Actions\Contact\Pipes;

use App\DTOs\MessageFlowInputDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Facades\Repository;
use App\Support\Whatsapp\Templates\AskQuestionTemplate;
use Closure;
use DateTime;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Enums\MessageStatus;

class AskCourse
{
    public const string STEP_ID = 'ask_course';

    public function __construct(protected AskQuestionTemplate $template) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageFlowInputDTO $data, Closure $next): ?Closure
    {
        if ($data->conversation->currentStep && $data->conversation->currentStep !== self::class) {
            return $next($data);
        }

        Repository::for(Conversation::class)->update($data->conversation->id, [
            'current_step' => self::class,
        ]);

        $alreadySent = Repository::for(Message::class)->exists([
            'conversation_id' => $data->conversation->id,
            'conversation_step' => self::STEP_ID,
        ]);

        if ($alreadySent) {
            return $next($data);
        }

        /** @var Message $message */
        $message = Repository::for(Message::class)->create([
            'conversation_id' => $data->conversation->id,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
        ]);

        $payload = $this->template->build($data->conversation, $message);

        /** @var Message $message */
        $message = Repository::for(Message::class)->update($message->id, [
            'payload' => $payload->values(),
        ]);

        if (!Messenger::send($message)) {
            return null;
        }

        Repository::for(Conversation::class)->update($data->conversation->id, [
            'current_step' => self::class,
        ]);

        return null;
    }
}
