<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Service\WhatsApp;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class WhatsAppHttpClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $accessToken,
        private string $baseUrl,
        private string $phoneNumberId
    ) {
    }

    public function post(string $endpoint, array $payload): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);

        $this->logger->info('WhatsApp API Request', [
            'url' => $url,
            'payload' => $payload,
        ]);

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $this->logger->info('WhatsApp API Response', [
            'status_code' => $response->getStatusCode(),
            'content' => $response->getContent(false),
        ]);

        return $response;
    }

    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        return sprintf('%s/%s/%s', $this->baseUrl, $this->phoneNumberId, $endpoint);
    }
}
