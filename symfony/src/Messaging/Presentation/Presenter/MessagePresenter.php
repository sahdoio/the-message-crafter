<?php

declare(strict_types=1);

namespace Messaging\Presentation\Presenter;

use Messaging\Domain\Entity\Message;
use Messaging\Domain\Entity\Conversation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class MessagePresenter
{
    public function presentMessage(Message $message): JsonResponse
    {
        return new JsonResponse([
            'id' => $message->getId(),
            'conversation_id' => $message->getConversationId(),
            'provider' => $message->getProvider()->value,
            'channel' => $message->getChannel()->value,
            'message_type' => $message->getMessageType()->value,
            'status' => $message->getStatus()->value,
            'sent_at' => $message->getSentAt()?->format('Y-m-d H:i:s'),
        ], Response::HTTP_OK);
    }

    public function presentConversation(Conversation $conversation): JsonResponse
    {
        return new JsonResponse([
            'id' => $conversation->getId(),
            'contact_id' => $conversation->getContactId(),
            'status' => $conversation->getStatus()->value,
            'strategy_class' => $conversation->getStrategyClass(),
            'current_step' => $conversation->getCurrentStep(),
            'started_at' => $conversation->getStartedAt()?->format('Y-m-d H:i:s'),
            'finished_at' => $conversation->getFinishedAt()?->format('Y-m-d H:i:s'),
        ], Response::HTTP_OK);
    }

    public function presentSuccess(string $message, array $data = []): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function presentError(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $details = []): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        if (!empty($details)) {
            $response['details'] = $details;
        }

        return new JsonResponse($response, $statusCode);
    }
}
