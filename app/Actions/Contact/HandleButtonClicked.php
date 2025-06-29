<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\MessageFlowInputDTO;
use Domain\Contact\Events\ButtonClicked;

class HandleButtonClicked
{
    public function __construct(protected FlowStrategyResolver $resolver) {}

    public function handle(ButtonClicked $event): void
    {
        $dto = new MessageFlowInputDTO(
            conversationId: $event->conversationId,
            messageId: $event->messageId,
            contactPhone: $event->contactPhone,
            buttonId: $event->buttonId,
            replyAction: $event->replyAction,
            extraInfo: $event->extraInfo
        );
        $strategy = $this->resolver->resolve($event->replyAction);
        $strategy->handle($dto);
    }
}

