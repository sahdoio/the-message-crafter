<?php

declare(strict_types=1);

namespace App\Services\Messenger\Whatsapp;

use App\Services\Messenger\Contracts\IMessenger;
use Domain\Contact\Entities\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Messenger implements IMessenger
{
    function send(Message $message): bool
    {
        $payload = $message->payload;

        Log::info('Sending message', ['data' => $payload]);

        $response = Http::whatsapp()->post('/messages', $payload);

        if (!$response->successful()) {
            Log::error('Error sending message', ['response' => $response->json()]);
            return false;
        }

        Log::info('Message sent', ['response' => $response->json()]);
        return true;
    }
}
