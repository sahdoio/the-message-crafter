<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\MessageFlowInputDTO;
use App\Facades\Repository;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\MessageStatus;
use Domain\Contact\Events\MessageReceived;
use Exception;
use Illuminate\Support\Facades\Log;

class HandleMessageReceived
{
    public function __construct(protected FlowStrategyResolver $resolver) {}

    /**
     * @throws Exception
     */
    public function handle(MessageReceived $event): void
    {
        if (!$this->dealWithMessage($event)) {
            Log::error('HandleMessageReceived - Message processing failed', [
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return;
        }

        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findById($event->conversationId);

        if (!$conversation->isActive()) {
            Log::warning('HandleMessageReceived - Conversation is not active', [
                'conversation_id' => $conversation->id,
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return;
        }

        // New conversation
        if (is_null($conversation->strategyClass)) {
            $strategy = $this->resolver->resolve($event->replyAction);
            $conversation->startStrategy($strategy::class);
        } // Existing conversation
        else {
            $strategy = app($conversation->strategyClass);
        }

        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->persistEntity($conversation);

        $data = new MessageFlowInputDTO(
            conversation: $conversation,
            messageId: $event->messageId,
            contactPhone: $event->contactPhone,
            replyAction: $event->replyAction,
            buttonId: $event->buttonId,
            extraInfo: $event->extraInfo
        );

        $strategy->handle(data: $data);
    }

    private function dealWithMessage(MessageReceived $event): bool
    {
        /** @var Message $message */
        $message = Repository::for(Message::class)->findOne(['id' => $event->messageId]);

        if ($message->status == MessageStatus::FINISHED->name) {
            Log::warning('HandleMessageReceived - Message already replied', [
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return false;
        }

        if ($event->buttonId) {
            /** @var MessageButton $messageButton */
            $messageButton = Repository::for(MessageButton::class)->findOne(['button_id' => $event->buttonId]);

            Repository::for(Message::class)->update($message->id, [
                'selected_button_id' => $messageButton->id,
            ]);
        }

        Repository::for(Message::class)->update($message->id, [
            'status' => MessageStatus::FINISHED->value,
        ]);

        return true;
    }
}

