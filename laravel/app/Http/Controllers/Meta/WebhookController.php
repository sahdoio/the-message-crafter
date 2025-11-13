<?php

declare(strict_types=1);

namespace App\Http\Controllers\Meta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('[WebhookController] Incoming webhook message: ' . json_encode($request->all()));

        $message = data_get($request->all(), 'entry.0.changes.0.value.messages.0');
        $businessPhoneId = data_get($request->all(), 'entry.0.changes.0.value.metadata.phone_number_id');

        if ($message && $message['type'] === 'text') {
            // Send reply
            Http::whatsapp()->post("/{$businessPhoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $message['from'],
                    'text' => ['payload' => 'Echo: ' . $message['text']['payload']],
                    'context' => ['message_id' => $message['id']],
                ]);

            // Mark as read
            Http::whatsapp()->post("/{$businessPhoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $message['id'],
                ]);
        }

        return response('', Response::HTTP_OK);
    }

    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('[WebhookController] Webhook verify attempt', [
            'mode' => $mode,
            'token' => $token,
            'challenge' => $challenge,
        ]);

        if ($mode === 'subscribe' && $token === config('whatsapp.webhook_verify_token')) {
            return response($challenge, Response::HTTP_OK);
        }

        return response('', Response::HTTP_FORBIDDEN);
    }
}
