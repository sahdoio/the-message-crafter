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

class AskCourse
{
    public const string STEP_ID = 'ask_course';

    public function __construct(protected AskQuestionTemplate $template) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageFlowInputDTO $data, Closure $next): ?Closure
    {
        if ($data->conversation->currentStep == self::class) {
            Log::info('AskCourse - Skipping step', [
                'conversation_id' => $data->conversation->id,
                'current_step' => $data->conversation->currentStep,
                'step' => self::STEP_ID,
            ]);
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
            Log::info('AskCourse - Message already sent', [
                'conversation_id' => $data->conversation->id,
                'step' => self::STEP_ID,
            ]);
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
            Log::error('AskCourse - Failed to send message', [
                'conversation_id' => $data->conversation->id,
                'message_id' => $message->id,
                'step' => self::STEP_ID,
            ]);
            return null;
        }

        Log::info('AskCourse - Message sent successfully', [
            'conversation_id' => $data->conversation->id,
            'message_id' => $message->id,
            'step' => self::STEP_ID,
        ]);

        Repository::for(Conversation::class)->update($data->conversation->id, [
            'current_step' => self::class,
        ]);

        return null;
    }
}
