<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Support\WhatsappTemplateBuilder;
use Domain\Contact\Repositories\IMessageRepository;

class SendMessage
{
    public function __construct(
        protected IMessageRepository      $messageRepository,
        protected WhatsappTemplateBuilder $builder
    ) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function exec(int $messageId): void
    {
        $message = $this->messageRepository->findById($messageId);

        if (!$message) {
            throw new ResourceNotFoundException('Message not found');
        }

        $payload = $this->builder->build($message);

        $this->messageRepository->update($message->id, [
            'payload' => $payload->values(),
        ]);

        Messenger::send($payload);
    }
}
