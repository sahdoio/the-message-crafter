<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Service\WhatsApp;

use Messaging\Domain\Entity\Message;
use Psr\Log\LoggerInterface;
use Shared\Domain\Service\MessengerServiceInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class WhatsAppMessengerService implements MessengerServiceInterface
{
    public function __construct(
        private WhatsAppHttpClient $httpClient,
        private LoggerInterface $logger
    ) {
    }

    public function send(Message $message): bool
    {
        $payload = $message->getPayload();

        if (empty($payload)) {
            $this->logger->error('Cannot send message: payload is empty', [
                'message_id' => $message->getId(),
            ]);
            return false;
        }

        $this->logger->info('Sending WhatsApp message', ['payload' => $payload]);

        try {
            $response = $this->httpClient->post('messages', $payload);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $this->logger->info('WhatsApp message sent successfully', [
                    'response' => $response->getContent(false),
                ]);
                return true;
            }

            $this->logger->error('Failed to send WhatsApp message', [
                'status_code' => $response->getStatusCode(),
                'response' => $response->getContent(false),
            ]);
            return false;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Transport error sending WhatsApp message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error sending WhatsApp message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
