<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserActivityMiddleware
{
    /**
     * Handle an incoming request and track user activity.
     * Updates last_activity_at timestamp every 5 minutes to avoid excessive database writes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only update if last activity was more than 5 minutes ago
            // This prevents excessive database writes on every request
            if (!$user->last_activity_at || $user->last_activity_at->diffInMinutes(now()) >= 5) {
                $user->update([
                    'last_activity_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
