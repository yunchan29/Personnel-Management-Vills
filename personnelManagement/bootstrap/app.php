<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Register global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // Register middleware for web routes (authenticated users)
        $middleware->appendToGroup('web', \App\Http\Middleware\UserActivityMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle throttle exceptions with clear cooldown messages
        $exceptions->render(function (Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
            $minutes = ceil($retryAfter / 60);
            $seconds = $retryAfter % 60;

            // Format time message
            $timeMessage = '';
            if ($minutes > 0 && $seconds > 0) {
                $timeMessage = "{$minutes} minute" . ($minutes > 1 ? 's' : '') . " and {$seconds} second" . ($seconds > 1 ? 's' : '');
            } elseif ($minutes > 0) {
                $timeMessage = "{$minutes} minute" . ($minutes > 1 ? 's' : '');
            } else {
                $timeMessage = "{$seconds} second" . ($seconds > 1 ? 's' : '');
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Too many attempts. Please try again in {$timeMessage}.",
                    'retry_after' => $retryAfter,
                    'retry_after_minutes' => $minutes,
                    'retry_after_seconds' => $seconds,
                    'errors' => [
                        'throttle' => ["You have made too many requests. Please wait {$timeMessage} before trying again."]
                    ]
                ], 429);
            }

            // Return HTML response for regular requests
            return back()->withErrors([
                'throttle' => "Too many attempts. Please try again in {$timeMessage}."
            ])->withInput();
        });
    })->create();
