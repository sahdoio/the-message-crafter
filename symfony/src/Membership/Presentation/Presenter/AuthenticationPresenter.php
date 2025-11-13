<?php

declare(strict_types=1);

namespace Membership\Presentation\Presenter;

use Membership\Application\DTO\UserApiLoginOutputDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthenticationPresenter
{
    public function present(UserApiLoginOutputDTO $outputDTO): JsonResponse
    {
        return new JsonResponse([
            'token' => $outputDTO->token,
            'user' => [
                'id' => $outputDTO->user->getId(),
                'name' => $outputDTO->user->getName(),
                'email' => $outputDTO->user->getEmail(),
                'created_at' => $outputDTO->user->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $outputDTO->user->getUpdatedAt()->format('Y-m-d H:i:s'),
            ],
        ], Response::HTTP_OK);
    }

    public function presentError(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'error' => $message,
        ], $statusCode);
    }
}
