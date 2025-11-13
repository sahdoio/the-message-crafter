<?php

declare(strict_types=1);

namespace Membership\Application\UseCase;

use Membership\Application\DTO\UserApiLoginInputDTO;
use Membership\Application\DTO\UserApiLoginOutputDTO;
use Membership\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserApiLoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function execute(UserApiLoginInputDTO $input): UserApiLoginOutputDTO
    {
        $user = $this->userRepository->findByEmail($input->email);

        if (!$user) {
            throw new UnauthorizedHttpException('', 'The provided credentials are incorrect.');
        }

        // In Symfony, we need to verify password using the password hasher
        // Note: This requires proper integration with Symfony Security component
        $isPasswordValid = password_verify($input->password, $user->getPassword());

        if (!$isPasswordValid) {
            throw new UnauthorizedHttpException('', 'The provided credentials are incorrect.');
        }

        // Delete old tokens
        $this->userRepository->deleteOldTokens($user->getId());

        // Create new API token
        $token = $this->userRepository->createUserToken($user->getId());

        return new UserApiLoginOutputDTO(
            token: $token,
            user: $user
        );
    }
}
