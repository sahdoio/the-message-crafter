<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Actions\Contact\StartConversation;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StartConversationRequest;
use Illuminate\Http\JsonResponse;

class StartConversationController extends Controller
{
    public function __construct(protected StartConversation $action) {}

    /**
     * @throws ResourceNotFoundException
     */
    public function exec(StartConversationRequest $request): JsonResponse
    {
        $this->action->handle($request->to);

        return response()->json(['message' => 'Message sent successfully']);
    }
}
