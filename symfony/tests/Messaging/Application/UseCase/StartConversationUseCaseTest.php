<?php

declare(strict_types=1);

namespace Tests\Messaging\Application\UseCase;

use Messaging\Application\UseCase\StartConversationUseCase;
use Messaging\Domain\Entity\Contact;
use Messaging\Domain\Entity\Conversation;
use Messaging\Domain\Entity\Message;
use Messaging\Domain\Repository\ContactRepositoryInterface;
use Messaging\Domain\Repository\ConversationRepositoryInterface;
use Messaging\Domain\Repository\MessageRepositoryInterface;
use Messaging\Domain\ValueObject\MessageBody;
use Messaging\Infrastructure\Service\Whatsapp\Templates\StartConversationTemplate;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Service\DomainEventBusInterface;

class StartConversationUseCaseTest extends TestCase
{
    public function testExecuteStartsConversationSuccessfully(): void
    {
        $contactRepository = $this->createMock(ContactRepositoryInterface::class);
        $messageRepository = $this->createMock(MessageRepositoryInterface::class);
        $conversationRepository = $this->createMock(ConversationRepositoryInterface::class);
        $template = $this->createMock(StartConversationTemplate::class);
        $eventBus = $this->createMock(DomainEventBusInterface::class);

        $useCase = new StartConversationUseCase(
            $contactRepository,
            $messageRepository,
            $conversationRepository,
            $template,
            $eventBus
        );

        $to = '+1234567890';
        $contact = $this->createMock(Contact::class);
        $conversation = $this->createMock(Conversation::class);
        $messageBody = $this->createMock(MessageBody::class);

        $contactRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['phone' => $to])
            ->willReturn($contact);

        $contact->expects($this->once())
            ->method('setDependencies')
            ->with($conversationRepository);

        $contact->expects($this->once())
            ->method('startConversation')
            ->willReturn($conversation);

        $conversation->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $template->expects($this->once())
            ->method('build')
            ->with($conversation, $this->isInstanceOf(Message::class))
            ->willReturn($messageBody);

        $template->expects($this->once())
            ->method('imageUrl')
            ->willReturn('http://example.com/image.jpg');
            
        $messageBody->expects($this->once())
            ->method('values')
            ->willReturn(['type' => 'template']);

        $messageRepository->expects($this->exactly(2))
            ->method('save')
            ->with($this->isInstanceOf(Message::class));

        $eventBus->expects($this->once())
            ->method('publishEntity')
            ->with($contact);

        $useCase->execute($to);
    }
}
