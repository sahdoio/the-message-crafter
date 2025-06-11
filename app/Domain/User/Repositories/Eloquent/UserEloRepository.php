<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories\Eloquent;

use App\BaseRepositories\Eloquent\EloRepository;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\IUserRepository;

class UserEloRepository extends EloRepository implements IUserRepository
{
    protected string $modelClass = User::class;

    public function createUserToken(int $userId): string
    {
        /**
         * Sanctum way to create a token
         * @var User $userModel
         */
        $userModel = $this->getEntity()->findOrFail($userId);

        return $userModel->createToken('api-token')->plainTextToken;
    }

    public function deleteOldTokens(int $userId): bool
    {
        /**
         * Sanctum way to delete all tokens
         * @var User $userModel
         */
        $userModel = $this->getEntity()->findOrFail($userId);

        $userModel->tokens()->delete();

        return true;
    }
}
