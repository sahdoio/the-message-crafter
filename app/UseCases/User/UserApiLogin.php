<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\DTOs\UserApiLoginInputDTO;
use App\DTOs\UserApiLoginOutputDTO;
use Domain\User\Repositories\IUserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserApiLogin
{
    public function __construct(protected IUserRepository $userRepository) {}

    public function handle(UserApiLoginInputDTO $data): UserApiLoginOutputDTO
    {
        $user = $this->userRepository->findOne(['email' => $data->email]);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['the provided credentials are incorrect.'],
            ]);
        }

        $this->userRepository->deleteOldTokens($user->id);

        $token = $this->userRepository->createUserToken($user->id);

        return new UserApiLoginOutputDTO(
            token: $token,
            user: $user
        );
    }
}
