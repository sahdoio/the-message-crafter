<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/conversations', name: 'api_conversations_')]
final class StartConversationController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        private readonly \Messaging\Application\UseCase\StartConversationUseCase $startConversationUseCase
    ) {
    }

    #[Route('/start', name: 'start', methods: ['POST'])]
    public function start(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON payload');
        }

        // Validate input
        $constraints = new Assert\Collection([
            'to' => [
                new Assert\NotBlank(message: 'Recipient phone number is required'),
                new Assert\Regex(
                    pattern: '/^\+?[1-9]\d{1,14}$/',
                    message: 'Invalid phone number format'
                ),
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
            $this->startConversationUseCase->execute($data['to']);

            $this->logger->info('Conversation start requested', [
                'to' => $data['to'],
            ]);

            return $this->json([
                'message' => 'Conversation started successfully',
                'to' => $data['to'],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Failed to start conversation', [
                'error' => $e->getMessage(),
                'to' => $data['to'] ?? null,
            ]);

            return $this->json([
                'error' => 'Failed to start conversation',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
