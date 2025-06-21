<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\User\Entities\User;
use Domain\User\Repositories\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    protected string $entityClass = User::class;

    public function createUserToken(int $userId): string
    {
        /**
         * Sanctum way to create a token
         * @var \App\Models\User $userModel
         */
        $userModel = $this->ormObject->findOrFail($userId);

        return $userModel->createToken('api-token')->plainTextToken;
    }

    public function deleteOldTokens(int $userId): bool
    {
        /**
         * Sanctum way to delete all tokens
         * @var \App\Models\User $userModel
         */
        $userModel = $this->ormObject->findOrFail($userId);

        $userModel->tokens()->delete();

        return true;
    }
}
