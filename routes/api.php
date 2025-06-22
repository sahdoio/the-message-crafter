<?php

use App\Http\Controllers\Auth\AuthenticationAPIController;
use App\Http\Controllers\Contact\StartConversationController;
use App\Http\Controllers\Meta\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'API Alive!';
});

route::post('/login', [AuthenticationAPIController::class, 'login']);
route::middleware('auth:sanctum')->post('/logout', [AuthenticationAPIController::class, 'logout']);

Route::get('/meta/webhook', [WebhookController::class, 'verify']);
Route::post('/meta/webhook', [WebhookController::class, 'handle']);

/**
 * Authenticated routes
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->makeHidden(['password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at']);
    });

    Route::post('start-conversation', [StartConversationController::class, 'exec'])->name('start-conversation');
});
