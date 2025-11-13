<?php

declare(strict_types=1);

namespace Messaging\Domain\Repository;

use Messaging\Domain\Entity\MessageButton;

interface MessageButtonRepositoryInterface
{
    public function findById(int $id): ?MessageButton;

    public function save(MessageButton $messageButton): void;

    public function delete(MessageButton $messageButton): void;

    /**
     * @return MessageButton[]
     */
    public function findByMessageId(int $messageId): array;
}
