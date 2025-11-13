<?php

declare(strict_types=1);

namespace Membership\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Membership\Domain\Entity\User;
use Membership\Domain\Repository\UserRepositoryInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function createUserToken(int $userId, string $tokenName = 'api-token'): string
    {
        // Generate a secure random token
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        // Store the hashed token in database
        $connection = $this->entityManager->getConnection();
        $connection->insert('api_tokens', [
            'user_id' => $userId,
            'token' => $hashedToken,
            'name' => $tokenName,
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        // Return the plain token (only time it's available)
        return $token;
    }

    public function deleteOldTokens(int $userId): bool
    {
        $connection = $this->entityManager->getConnection();
        $connection->delete('api_tokens', ['user_id' => $userId]);

        return true;
    }

    public function deleteCurrentToken(int $userId, string $tokenId): bool
    {
        $connection = $this->entityManager->getConnection();
        $connection->delete('api_tokens', [
            'user_id' => $userId,
            'id' => $tokenId,
        ]);

        return true;
    }

    public function findByToken(string $token): ?User
    {
        $hashedToken = hash('sha256', $token);

        $connection = $this->entityManager->getConnection();
        $result = $connection->fetchAssociative(
            'SELECT user_id FROM api_tokens WHERE token = :token',
            ['token' => $hashedToken]
        );

        if (!$result) {
            return null;
        }

        return $this->findById((int) $result['user_id']);
    }
}
