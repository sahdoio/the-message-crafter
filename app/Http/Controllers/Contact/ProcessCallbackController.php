<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Actions\Contact\ProcessMessageCallback;
use App\DTOs\ProcessMessageCallbackInputDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessCallbackRequest;
use Illuminate\Http\JsonResponse;

class ProcessCallbackController extends Controller
{
    public function __construct(protected ProcessMessageCallback $action) {}

    public function exec(ProcessCallbackRequest $request): JsonResponse
    {
        $this->action->handle(new ProcessMessageCallbackInputDTO(
            messageId: $request->messageId(),
            recipientId: $request->recipientId(),
            buttonReply: $request->buttonReply(),
            text: $request->textMessage(),
            errors: $request->errorsList()
        ));

        return response()->json(['message' => 'Callback processed successfully']);
    }
}
