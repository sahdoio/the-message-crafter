<?php

declare(strict_types=1);

namespace App\Presenters;

use App\DTOs\UserApiLoginOutputDTO;
use Illuminate\Http\JsonResponse;

class AuthenticationPresenter
{
    public function handle(UserApiLoginOutputDTO $outputDTO): array
    {
        return [
            'token' => $outputDTO->token,
            'user' => [
                'id' => $outputDTO->user->id,
                'name' => $outputDTO->user->name,
                'email' => $outputDTO->user->email,
            ],
        ];
    }

    public function asJson(UserApiLoginOutputDTO $outputDTO): JsonResponse
    {
        return response()->json($this->handle($outputDTO));
    }
}
