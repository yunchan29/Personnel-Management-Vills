<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Check if account is locked
     */
    public static function isAccountLocked(string $email): bool
    {
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $failedAttempts = self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes($lockoutMinutes))
            ->count();

        return $failedAttempts >= $maxAttempts;
    }

    /**
     * Get remaining lockout time in minutes
     */
    public static function getRemainingLockoutTime(string $email): int
    {
        $lockoutMinutes = 15;

        $oldestFailedAttempt = self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>', Carbon::now()->subMinutes($lockoutMinutes))
            ->orderBy('attempted_at', 'asc')
            ->first();

        if (!$oldestFailedAttempt) {
            return 0;
        }

        $unlockTime = $oldestFailedAttempt->attempted_at->addMinutes($lockoutMinutes);
        $remainingMinutes = Carbon::now()->diffInMinutes($unlockTime, false);

        return max(0, (int) $remainingMinutes);
    }

    /**
     * Clear successful login attempts
     */
    public static function clearAttempts(string $email): void
    {
        self::where('email', $email)
            ->where('successful', false)
            ->delete();
    }

    /**
     * Record login attempt
     */
    public static function recordAttempt(string $email, bool $successful): void
    {
        self::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'attempted_at' => Carbon::now(),
        ]);
    }
}
