<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Messaging\Domain\Entity\Contact;
use Messaging\Domain\Repository\ContactRepositoryInterface;

final class DoctrineContactRepository implements ContactRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findById(int $id): ?Contact
    {
        return $this->entityManager->find(Contact::class, $id);
    }

    public function findByEmail(string $email): ?Contact
    {
        return $this->entityManager->getRepository(Contact::class)
            ->findOneBy(['email' => $email]);
    }

    public function findByPhone(string $phone): ?Contact
    {
        return $this->entityManager->getRepository(Contact::class)
            ->findOneBy(['phone' => $phone]);
    }

    public function save(Contact $contact): void
    {
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }

    public function delete(Contact $contact): void
    {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Contact::class)
            ->findAll();
    }
}
