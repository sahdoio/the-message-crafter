<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Http\Controller;

use Psr\Log\LoggerInterface;
use Shared\Infrastructure\Service\WhatsApp\WhatsAppHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/api/webhooks/whatsapp', name: 'api_webhooks_whatsapp_')]
final class WebhookController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly WhatsAppHttpClient $whatsAppClient,
        #[Autowire('%whatsapp.webhook_verify_token%')]
        private readonly string $webhookVerifyToken
    ) {
    }

    #[Route('', name: 'handle', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $this->logger->info('[WebhookController] Incoming webhook message', [
            'payload' => $payload,
        ]);

        // Extract message from WhatsApp webhook structure
        $message = $payload['entry'][0]['changes'][0]['value']['messages'][0] ?? null;
        $businessPhoneId = $payload['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'] ?? null;

        if (!$message || !$businessPhoneId) {
            $this->logger->warning('Invalid webhook payload structure');
            return new Response('', Response::HTTP_OK);
        }

        try {
            // Handle text messages
            if ($message['type'] === 'text') {
                $messageText = $message['text']['body'] ?? '';
                $from = $message['from'] ?? '';
                $messageId = $message['id'] ?? '';

                $this->logger->info('Processing text message', [
                    'from' => $from,
                    'text' => $messageText,
                    'message_id' => $messageId,
                ]);

                // Send echo reply
                $this->whatsAppClient->post('messages', [
                    'messaging_product' => 'whatsapp',
                    'to' => $from,
                    'text' => ['body' => 'Echo: ' . $messageText],
                    'context' => ['message_id' => $messageId],
                ]);

                // Mark as read
                $this->whatsAppClient->post('messages', [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $messageId,
                ]);

                $this->logger->info('Message processed and replied', [
                    'message_id' => $messageId,
                ]);
            } elseif ($message['type'] === 'interactive') {
                // Handle interactive messages (button/list replies)
                $this->logger->info('Interactive message received', [
                    'type' => $message['interactive']['type'] ?? 'unknown',
                    'message' => $message,
                ]);

                // TODO: Implement interactive message handling
                // This would trigger the message flow based on button selection
            }
        } catch (\Exception $e) {
            $this->logger->error('Error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Still return 200 to acknowledge receipt
            return new Response('', Response::HTTP_OK);
        }

        return new Response('', Response::HTTP_OK);
    }

    #[Route('', name: 'verify', methods: ['GET'])]
    public function verify(Request $request): Response
    {
        $mode = $request->query->get('hub_mode');
        $token = $request->query->get('hub_verify_token');
        $challenge = $request->query->get('hub_challenge');

        $this->logger->info('[WebhookController] Webhook verification attempt', [
            'mode' => $mode,
            'token' => $token ? 'present' : 'missing',
            'challenge' => $challenge ? 'present' : 'missing',
        ]);

        if ($mode === 'subscribe' && $token === $this->webhookVerifyToken) {
            $this->logger->info('Webhook verification successful');
            return new Response($challenge, Response::HTTP_OK);
        }

        $this->logger->warning('Webhook verification failed', [
            'mode' => $mode,
            'token_match' => $token === $this->webhookVerifyToken,
        ]);

        return new Response('Forbidden', Response::HTTP_FORBIDDEN);
    }
}
