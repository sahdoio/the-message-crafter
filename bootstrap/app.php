<?php

use App\Exceptions\ResourceNotFoundException;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Domain\Contact\Exceptions\ConversationAlreadyStartedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (DomainException $e) {
            Log::warning('Domain error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        });

        $exceptions->report(function (ResourceNotFoundException $e) {
            Log::warning('Resource not found', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        });

        $exceptions->report(function (Exception $e) {
            Log::error('Internal server error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        });

        $exceptions->render(function (ResourceNotFoundException $e) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (ConversationAlreadyStartedException $e) {
            return response()->json([
                'error' => 'Conversation already started',
                'message' => $e->getMessage(),
            ], Response::HTTP_CONFLICT);
        });

        // Ignore specific exception types
        $exceptions->dontReport([]);
    })->create();
