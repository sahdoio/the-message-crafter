<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Actions\Contact\Strategies\IMessageFlow;
use App\DTOs\MessageFlowInputDTO;
use App\Facades\Repository;
use Domain\Contact\Entities\Conversation;
use Domain\Contact\Events\ButtonClicked;

class HandleButtonClicked
{
    public function __construct(protected FlowStrategyResolver $resolver) {}

    public function handle(ButtonClicked $event): void
    {
        /** @var Conversation $conversation */
        $conversation = Repository::for(Conversation::class)->findById($event->conversationId);

        // New conversation
        if (is_null($conversation->strategyClass)) {
            $strategy = $this->resolver->resolve($event->replyAction);
        }
        // Existing conversation
        else {
            /** @var IMessageFlow $strategy */
            $strategy = app($conversation->strategyClass);
        }

        $conversation->startStrategy($strategy::class);
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
}

