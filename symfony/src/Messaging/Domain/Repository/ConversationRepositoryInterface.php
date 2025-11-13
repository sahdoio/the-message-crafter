<?php

declare(strict_types=1);

namespace Messaging\Domain\Repository;

use Messaging\Domain\Entity\Conversation;

interface ConversationRepositoryInterface
{
    public function findById(int $id): ?Conversation;

    public function save(Conversation $conversation): void;

    public function delete(Conversation $conversation): void;

    public function hasActiveFor(int $contactId): bool;

    public function findActiveByContactId(int $contactId): ?Conversation;

    /**
     * @return Conversation[]
     */
    public function findByContactId(int $contactId): array;
}
