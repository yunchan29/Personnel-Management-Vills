<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait ApiResponseTrait
{
    /**
     * Return JSON response for AJAX requests or redirect for traditional requests
     */
    protected function successResponse(
        string $message,
        ?string $redirectUrl = null,
        array $data = []
    ): JsonResponse|RedirectResponse {
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json(array_merge([
                'success' => true,
                'message' => $message,
                'redirect' => $redirectUrl,
            ], $data));
        }

        // Handle null redirect URL for non-AJAX requests
        if ($redirectUrl === null) {
            return back()->with('success', $message);
        }

        return redirect($redirectUrl)->with('success', $message);
    }

    /**
     * Return JSON error response for AJAX or redirect back for traditional requests
     */
    protected function errorResponse(
        string|array $errors,
        string $message = 'Validation failed.',
        array $input = [],
        int $statusCode = 422
    ): JsonResponse|RedirectResponse {
        // Normalize errors to array format
        if (is_string($errors)) {
            $errors = ['error' => [$errors]];
        }

        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], $statusCode);
        }

        $redirect = back()->withErrors($errors);

        if (!empty($input)) {
            $redirect = $redirect->withInput($input);
        } elseif (count(request()->all()) > 0) {
            $redirect = $redirect->onlyInput(array_keys($input ?: request()->all()));
        }

        return $redirect;
    }

    /**
     * Return locked account response
     */
    protected function accountLockedResponse(
        string $email,
        int $remainingMinutes
    ): JsonResponse|RedirectResponse {
        $message = "Account temporarily locked due to multiple failed login attempts. Please try again in {$remainingMinutes} minute(s).";

        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => ['email' => [$message]],
                'locked' => true,
                'remaining_minutes' => $remainingMinutes
            ], 429);
        }

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }
}
