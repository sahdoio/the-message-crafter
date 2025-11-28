<?php

declare(strict_types=1);

namespace Messaging\Application\UseCase;

use Messaging\Domain\Entity\Conversation;
use Messaging\Domain\Entity\Message;
use Messaging\Domain\Enum\MessageStatus;
use Messaging\Domain\Repository\ContactRepositoryInterface;
use Messaging\Domain\Repository\ConversationRepositoryInterface;
use Messaging\Domain\Repository\MessageRepositoryInterface;
use Messaging\Infrastructure\Service\Whatsapp\Templates\StartConversationTemplate;
use Shared\Domain\Service\DomainEventBusInterface;
use DateTime;

class StartConversationUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly MessageRepositoryInterface $messageRepository,
        private readonly ConversationRepositoryInterface $conversationRepository,
        private readonly StartConversationTemplate $template,
        private readonly DomainEventBusInterface $domainEventBus
    ) {
    }

    public function execute(string $to): void
    {
        $contact = $this->contactRepository->findOneBy(['phone' => $to]);

        if (!$contact) {
             throw new \RuntimeException('Contact not found');
        }

        $contact->setDependencies($this->conversationRepository);
        $conversation = $contact->startConversation();

        $message = Message::create($conversation->getId());
        $message->setConversationStep(self::class);
        
        $this->messageRepository->save($message);

        $whatsappPayload = $this->template->build($conversation, $message);

        $message->setPayload($whatsappPayload->values());
        $message->setImageUrl($this->template->imageUrl());
        
        $this->messageRepository->save($message);

        $this->domainEventBus->publishEntity($contact);
    }
}
