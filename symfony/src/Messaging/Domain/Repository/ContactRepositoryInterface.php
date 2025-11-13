<?php

declare(strict_types=1);

namespace Messaging\Domain\Repository;

use Messaging\Domain\Entity\Contact;

interface ContactRepositoryInterface
{
    public function findById(int $id): ?Contact;

    public function findByEmail(string $email): ?Contact;

    public function findByPhone(string $phone): ?Contact;

    public function save(Contact $contact): void;

    public function delete(Contact $contact): void;

    /**
     * @return Contact[]
     */
    public function findAll(): array;
}
