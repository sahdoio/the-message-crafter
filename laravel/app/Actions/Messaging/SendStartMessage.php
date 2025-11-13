<?php

declare(strict_types=1);

namespace App\Actions\Messaging;

use App\DTOs\FilterOptionsDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Support\Whatsapp\Templates\StartConversationTemplate;
use Domain\Messaging\Enums\MessageStatus;
use Domain\Messaging\Repositories\IConversationRepository;
use Domain\Messaging\Repositories\IMessageRepository;

class SendStartMessage
{
    public function __construct(
        protected IConversationRepository   $conversationRepository,
        protected IMessageRepository        $messageRepository,
        protected StartConversationTemplate $builder
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function handle(int $conversationId): void
    {
        $conversation = $this->conversationRepository->findById($conversationId);

        $message = $this->messageRepository->findOne([
            'conversation_id' => $conversation->id,
            'status' => MessageStatus::SENT,
        ], new FilterOptionsDTO(
            orderBy: 'created_at',
        ));

        if (!$message) {
            throw new ResourceNotFoundException('Message not found');
        }

        Messenger::send($message);
    }
}
