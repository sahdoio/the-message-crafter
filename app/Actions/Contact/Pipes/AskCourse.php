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
use Illuminate\Support\Facades\Log;

class AskCourse extends ConversationPipe
{
    public function __construct(protected AskQuestionTemplate $template) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageFlowInputDTO $data, Closure $next): ?Closure
    {
        if (($callback = $this->verifyStep($data, $next)) instanceof Closure) {
            return $callback;
        }

        /** @var Message $message */
        $message = Repository::for(Message::class)->create([
            'conversation_id' => $data->conversation->id,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
            'conversation_step' => self::class,
        ]);

        $payload = $this->template->build($data->conversation, $message);

        /** @var Message $message */
        $message = Repository::for(Message::class)->update($message->id, [
            'payload' => $payload->values(),
        ]);

        if (!Messenger::send($message)) {
            Log::error('AskCourse - Failed to send message', [
                'conversation_id' => $data->conversation->id,
                'message_id' => $message->id,
                'step' => self::class,
            ]);
            return null;
        }

        Log::info('AskCourse - Message sent successfully', [
            'conversation_id' => $data->conversation->id,
            'message_id' => $message->id,
            'step' => self::class,
        ]);

        $this->updateStep($data);

        return null;
    }
}
