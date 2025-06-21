<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Actions\Contact\SendMessage;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendMessageController extends Controller
{
    public function __construct(protected SendMessage $sendMessage) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function exec(Request $request): JsonResponse
    {
        $request->validate([
            'to' => ['required', 'string'],
        ]);

        $this->sendMessage->handle($request->to);

        return response()->json(['message' => 'Message sent successfully']);
    }
}
