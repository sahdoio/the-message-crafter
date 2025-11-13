<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Messaging\Domain\Entity\Conversation;
use Messaging\Domain\Enum\ConversationStatus;
use Messaging\Domain\Repository\ConversationRepositoryInterface;

final class DoctrineConversationRepository implements ConversationRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findById(int $id): ?Conversation
    {
        return $this->entityManager->find(Conversation::class, $id);
    }

    public function save(Conversation $conversation): void
    {
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
    }

    public function delete(Conversation $conversation): void
    {
        $this->entityManager->remove($conversation);
        $this->entityManager->flush();
    }

    public function hasActiveFor(int $contactId): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $count = $qb->select('COUNT(c.id)')
            ->from(Conversation::class, 'c')
            ->where('c.contactId = :contactId')
            ->andWhere('c.status = :status')
            ->setParameter('contactId', $contactId)
            ->setParameter('status', ConversationStatus::ACTIVE)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function findActiveByContactId(int $contactId): ?Conversation
    {
        return $this->entityManager->getRepository(Conversation::class)
            ->findOneBy([
                'contactId' => $contactId,
                'status' => ConversationStatus::ACTIVE,
            ]);
    }

    public function findByContactId(int $contactId): array
    {
        return $this->entityManager->getRepository(Conversation::class)
            ->findBy(['contactId' => $contactId], ['createdAt' => 'DESC']);
    }
}
