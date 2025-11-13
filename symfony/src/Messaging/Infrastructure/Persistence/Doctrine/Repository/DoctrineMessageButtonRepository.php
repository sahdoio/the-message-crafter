<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Messaging\Domain\Entity\MessageButton;
use Messaging\Domain\Repository\MessageButtonRepositoryInterface;

final class DoctrineMessageButtonRepository implements MessageButtonRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findById(int $id): ?MessageButton
    {
        return $this->entityManager->find(MessageButton::class, $id);
    }

    public function save(MessageButton $messageButton): void
    {
        $this->entityManager->persist($messageButton);
        $this->entityManager->flush();
    }

    public function delete(MessageButton $messageButton): void
    {
        $this->entityManager->remove($messageButton);
        $this->entityManager->flush();
    }

    public function findByMessageId(int $messageId): array
    {
        return $this->entityManager->getRepository(MessageButton::class)
            ->findBy(['messageId' => $messageId]);
    }
}
