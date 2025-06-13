<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTOs\UserApiLoginInputDTO;
use App\Http\Controllers\Controller;
use App\Presenters\AuthenticationPresenter;
use App\Actions\User\UserApiLogin;
use Illuminate\Http\Request;

class AuthenticationAPIController extends Controller
{
    public function __construct(
        private UserApiLogin            $userApiLogin,
        private AuthenticationPresenter $presenter
    )
    {
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $result = $this->userApiLogin->handle(new UserApiLoginInputDTO(
            email: $credentials['email'],
            password: $credentials['password']
        ));

        return $this->presenter->asJson($result);
    }

    public function logout(Request $request)
    {
        // TODO - Migrate to UseCase - Remove the logic from the controller

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out']);
    }
}
