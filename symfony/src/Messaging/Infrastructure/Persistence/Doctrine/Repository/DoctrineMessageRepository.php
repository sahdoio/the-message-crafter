<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Messaging\Domain\Entity\Message;
use Messaging\Domain\Repository\MessageRepositoryInterface;

final class DoctrineMessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findById(int $id): ?Message
    {
        return $this->entityManager->find(Message::class, $id);
    }

    public function save(Message $message): void
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function delete(Message $message): void
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }

    public function findByConversationId(int $conversationId): array
    {
        return $this->entityManager->getRepository(Message::class)
            ->findBy(['conversationId' => $conversationId], ['createdAt' => 'ASC']);
    }
}
