<?php

declare(strict_types=1);

namespace Membership\Domain\Repository;

use Membership\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function delete(User $user): void;

    public function createUserToken(int $userId, string $tokenName = 'api-token'): string;

    public function deleteOldTokens(int $userId): bool;

    public function deleteCurrentToken(int $userId, string $tokenId): bool;
}
