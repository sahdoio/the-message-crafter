<?php

declare(strict_types=1);

namespace Membership\Application\UseCase;

use Membership\Domain\Repository\UserRepositoryInterface;

final readonly class UserApiLogoutUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(int $userId, string $tokenId): bool
    {
        return $this->userRepository->deleteCurrentToken($userId, $tokenId);
    }
}
