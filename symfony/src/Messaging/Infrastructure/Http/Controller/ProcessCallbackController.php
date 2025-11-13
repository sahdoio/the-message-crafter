<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Http\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/callbacks', name: 'api_callbacks_')]
final class ProcessCallbackController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/process', name: 'process', methods: ['POST'])]
    public function process(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            $this->logger->warning('Invalid callback payload received');

            return $this->json([
                'error' => 'Invalid payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->logger->info('Callback received', [
            'payload' => $data,
        ]);

        try {
            // Extract callback data
            $messageId = $data['message_id'] ?? null;
            $recipientId = $data['recipient_id'] ?? null;
            $buttonReply = $data['button_reply'] ?? null;
            $textMessage = $data['text_message'] ?? null;
            $errors = $data['errors'] ?? [];

            if (!$messageId || !$recipientId) {
                return $this->json([
                    'error' => 'Missing required fields: message_id and recipient_id',
                ], Response::HTTP_BAD_REQUEST);
            }

            // TODO: Implement ProcessMessageCallbackUseCase
            // $inputDTO = new ProcessMessageCallbackInputDTO(
            //     messageId: $messageId,
            //     recipientId: $recipientId,
            //     buttonReply: $buttonReply,
            //     text: $textMessage,
            //     errors: $errors
            // );
            // $this->processMessageCallbackUseCase->execute($inputDTO);

            $this->logger->info('Callback processed successfully', [
                'message_id' => $messageId,
                'recipient_id' => $recipientId,
            ]);

            return $this->json([
                'message' => 'Callback processed successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Failed to process callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->json([
                'error' => 'Failed to process callback',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
