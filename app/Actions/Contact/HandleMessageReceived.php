<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\MessageFlowInputDTO;
use App\Facades\Repository;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
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
            Log::error('HandleButtonClicked - Message processing failed', [
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return;
        }

        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findById($event->conversationId);

        if (!$conversation) {
            Log::error('HandleButtonClicked - Conversation not found', [
                'conversation_id' => $event->conversationId,
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return;
        }

        if (!$conversation->isActive()) {
            Log::warning('HandleButtonClicked - Conversation is not active', [
                'conversation_id' => $conversation->id,
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return;
        }

        $strategy = is_null($conversation->strategyClass) ?
            // New conversation
            $this->resolver->resolve($event->replyAction) :
            // Existing conversation
            app($conversation->strategyClass);

        $conversation->startStrategy($strategy::class);

        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->persistEntity($conversation);

        $data = new MessageFlowInputDTO(
            conversation: $conversation,
            messageId: $event->messageId,
            contactPhone: $event->contactPhone,
            buttonId: $event->buttonId,
            replyAction: $event->replyAction,
            extraInfo: $event->extraInfo
        );

        $strategy->handle(data: $data);
    }

    private function dealWithMessage(MessageReceived $event): bool
    {
        /** @var Message $message */
        $message = Repository::for(Message::class)->findOne(['id' => $event->messageId]);

        if (!$message) {
            Log::error('HandleButtonClicked - Message not found', [
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return false;
        }

        if ($message->selectedButtonId) {
            Log::warning('HandleButtonClicked - Message already replied', [
                'message_id' => $event->messageId,
                'button_id' => $event->buttonId,
            ]);
            return false;
        }

        /** @var MessageButton $messageButton */
        $messageButton = Repository::for(MessageButton::class)->findOne(['button_id' => $event->buttonId]);

        if (!$messageButton) {
            Log::error('HandleButtonClicked - MessageButton not found', [
                'button_id' => $event->buttonId,
                'message_id' => $event->messageId,
            ]);
            return false;
        }

        Repository::for(Message::class)->update($message->id, [
            'selected_button_id' => $messageButton->id,
        ]);

        return true;
    }
}

