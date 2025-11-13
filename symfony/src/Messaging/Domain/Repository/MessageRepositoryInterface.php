<?php

declare(strict_types=1);

namespace Messaging\Domain\Repository;

use Messaging\Domain\Entity\Message;

interface MessageRepositoryInterface
{
    public function findById(int $id): ?Message;

    public function save(Message $message): void;

    public function delete(Message $message): void;

    /**
     * @return Message[]
     */
    public function findByConversationId(int $conversationId): array;
}
