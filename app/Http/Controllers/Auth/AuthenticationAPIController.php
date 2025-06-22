<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTOs\UserApiLoginInputDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserApiLoginRequest;
use App\Presenters\AuthenticationPresenter;
use App\Actions\User\UserApiLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationAPIController extends Controller
{
    public function __construct(
        private UserApiLogin            $userApiLogin,
        private AuthenticationPresenter $presenter
    ) {}

    public function login(UserApiLoginRequest $request): JsonResponse
    {
        $result = $this->userApiLogin->handle(new UserApiLoginInputDTO(
            email: $request->email,
            password: $request->password
        ));

        return $this->presenter->asJson($result);
    }

    public function logout(Request $request): JsonResponse
    {
        // TODO - Migrate to an Action - Remove this logic from the controller

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out']);
    }
}
