<?php

declare(strict_types=1);

namespace App\Actions\Contact\Pipes;

use App\DTOs\MessageFlowInputDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Facades\Repository;
use App\Support\Whatsapp\Templates\SimpleTextTemplate;
use Closure;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Enums\MessageStatus;
use Datetime;

class AskCourse
{
    public function __construct(protected SimpleTextTemplate $template) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(MessageFlowInputDTO $data, Closure $next)
    {
        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findOne([
            'id' => $data->conversationId,
        ]);

        $payload = $this->template->build($conversation, 'What course would you like to enroll in?');

        /** @var Message $message */
        $message = Repository::for(Message::class)->create([
            'conversation_id' => $data->conversationId,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
            'payload' => $payload->values(),
        ]);

        if (!Messenger::send($message)) {
            return null; // stops pipeline
        }

        return $next($data); // pass to next step
    }
}
