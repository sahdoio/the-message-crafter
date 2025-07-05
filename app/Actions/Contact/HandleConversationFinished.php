<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Events\ConversationFinishedEvent;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Facades\Repository;
use App\Support\Whatsapp\Templates\FinishConversationTemplate;
use Datetime;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Enums\MessageStatus;
use Illuminate\Support\Facades\Log;

class HandleConversationFinished
{
    public function __construct(protected FinishConversationTemplate $template) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(ConversationFinishedEvent $event): void
    {
        $data = $event->conversationFinished;

        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findById($data->conversationId);

        /** @var Message $message */
        $message = Repository::for(Message::class)->create([
            'conversation_id' => $data->conversationId,
            'status' => MessageStatus::SENT->value,
            'sent_at' => new DateTime()->format('Y-m-d H:i:s'),
        ]);

        $payload = $this->template->build($conversation);

        /** @var Message $message */
        $message = Repository::for(Message::class)->update($message->id, [
            'payload' => $payload->values(),
        ]);

        Messenger::send($message);

        Log::info('HandleConversationFinished - Conversation finished', [
            'conversation_id' => $data->conversationId,
            'message_id' => $message->id,
        ]);
    }
}
