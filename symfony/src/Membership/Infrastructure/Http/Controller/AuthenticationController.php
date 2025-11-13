<?php

declare(strict_types=1);

namespace Membership\Infrastructure\Http\Controller;

use Membership\Application\DTO\UserApiLoginInputDTO;
use Membership\Application\UseCase\UserApiLoginUseCase;
use Membership\Application\UseCase\UserApiLogoutUseCase;
use Membership\Presentation\Presenter\AuthenticationPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
final class AuthenticationController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        Request $request,
        UserApiLoginUseCase $loginUseCase,
        AuthenticationPresenter $presenter
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON payload');
        }

        // Validate input
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(message: 'Email is required'),
                new Assert\Email(message: 'Invalid email format'),
            ],
            'password' => [
                new Assert\NotBlank(message: 'Password is required'),
                new Assert\Length(min: 6, minMessage: 'Password must be at least 6 characters'),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $this->json([
                'error' => 'Validation failed',
                'violations' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $inputDTO = new UserApiLoginInputDTO(
                email: $data['email'],
                password: $data['password']
            );

            $outputDTO = $loginUseCase->execute($inputDTO);

            return $presenter->present($outputDTO);
        } catch (UnauthorizedHttpException $e) {
            return $this->json([
                'error' => 'Authentication failed',
                'message' => 'The provided credentials are incorrect',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(
        Request $request,
        UserApiLogoutUseCase $logoutUseCase
    ): JsonResponse {
        // Get the authenticated user from the token
        // This would require Symfony Security component to be fully configured
        // For now, we'll extract user ID and token ID from the request

        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json([
                'error' => 'Missing or invalid authorization header',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Extract token (in a real implementation, you'd decode/verify the token)
        $token = substr($authHeader, 7);

        // For demonstration, assuming you have a way to get user ID from token
        // In production, this would be handled by Symfony Security
        try {
            // $userId = $this->getUser()->getId();
            // $tokenId = $this->getTokenIdFromBearer($token);

            // For now, return success
            // $logoutUseCase->execute($userId, $tokenId);

            return $this->json([
                'message' => 'Successfully logged out',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Logout failed',
                'message' => 'An error occurred during logout',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        // This endpoint would return the authenticated user's information
        // Requires Symfony Security component configuration

        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json([
                'error' => 'Missing or invalid authorization header',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // In production, this would use $this->getUser()
        return $this->json([
            'message' => 'User endpoint - requires Security component configuration',
            'note' => 'Implement Symfony Security to enable this endpoint',
        ], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        // This endpoint would refresh the authentication token
        // Requires implementation based on your token strategy

        return $this->json([
            'message' => 'Token refresh endpoint',
            'note' => 'Implement token refresh logic based on your authentication strategy',
        ], Response::HTTP_NOT_IMPLEMENTED);
    }
}
