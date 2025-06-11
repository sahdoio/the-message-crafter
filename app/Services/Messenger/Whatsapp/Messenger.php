<?php

declare(strict_types=1);

namespace App\Services\Messenger\Whatsapp;

use App\Domain\Contact\DTOs\SendMessageInputDTO;
use App\Domain\Contact\VOs\MessageBody;
use App\Services\Messenger\Contracts\IMessenger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Messenger implements IMessenger
{
    function send(MessageBody|SendMessageInputDTO $data): bool
    {
        Log::info('Sending message', ['data' => $data->values()]);

        $response = Http::whatsapp()->post('/messages', $data->values());

        if (!$response->successful()) {
            Log::error('Error sending message', ['response' => $response->json()]);
            return false;
        }

        Log::info('Message sent', ['response' => $response->json()]);
        return true;
    }
}
