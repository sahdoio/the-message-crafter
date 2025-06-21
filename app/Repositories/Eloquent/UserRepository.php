<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use Domain\User\Repositories\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
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
