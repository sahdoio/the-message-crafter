<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // None of these fields are required, as the request can contain various types of data.
            // Meta must receive always an ok status.
        ];
    }

    public function messageId(): string
    {
        return data_get($this, 'entry.0.changes.0.value.messages.0.id');
    }

    public function recipientId(): string
    {
        return data_get($this, 'entry.0.changes.0.value.contacts.0.wa_id');
    }

    public function buttonReply(): array
    {
        $message = data_get($this, 'entry.0.changes.0.value.messages.0', []);

        if (!isset($message['type']) || $message['type'] !== 'interactive') {
            return data_get($message, 'button', []);
        }

        return match ($message['interactive']['type'] ?? null) {
            'button_reply' => data_get($message, 'interactive.button_reply', []),
            'list_reply' => data_get($message, 'interactive.list_reply', []),
            default => [],
        };
    }

    public function errorsList(): array
    {
        return data_get($this, 'entry.0.changes.0.value.errors', []);
    }
}
