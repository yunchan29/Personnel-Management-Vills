<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Resume;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'gender',

        'birth_date',
        'birth_place',
        'age',

        'civil_status',
        'religion',
        'nationality',

        'email',
        'password',
        'mobile_number',
        'profile_picture',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',

        'full_address',
        'province',
        'city',
        'barangay',
        'uuid',
        'street_details',
        'postal_code',
        'active_status',
        'job_industry',

        'last_login_at',
        'last_activity_at',
        'last_login_ip',
    ];

    /**
     * The attributes that are guarded from mass assignment.
     * These fields can only be set through direct assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'role',           // Prevent role escalation attacks
        'is_archived',    // Prevent users from archiving themselves
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_archived' => 'boolean', // ✅ for archiving
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate a UUID when creating a new user.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();
        });
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Use UUID for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the user's profile picture URL.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture
            ? asset('storage/' . $this->profile_picture)
            : asset('images/default.png');
    }


    /**
 * Determine if the user's profile is complete.
 */
public function getIsProfileCompleteAttribute(): bool
{
    return $this->first_name &&
           $this->last_name &&
           $this->gender &&
           $this->birth_date &&
           $this->civil_status &&
           $this->nationality &&
           $this->mobile_number &&
           $this->full_address &&
           $this->province &&
           $this->city &&
           $this->barangay &&
           $this->profile_picture &&
           $this->active_status === 'Active';
}

public function resume()
    {
        return $this->hasOne(Resume::class);
    }

    public function file201()
{
    return $this->hasOne(File201::class);
}


public function applications()
{
    return $this->hasMany(Application::class);
}

public function workExperiences()
{
    return $this->hasMany(WorkExperience::class);
}

public function job()
{
    return $this->belongsTo(Job::class);
}

/**
 * Generate a 6-digit verification code and set expiration
 */
public function generateVerificationCode(): string
{
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $this->verification_code = $code;
    $this->verification_code_expires_at = now()->addMinutes(15); // Code expires in 15 minutes
    $this->save();

    return $code;
}

/**
 * Verify the provided code
 */
public function verifyCode(string $code): bool
{
    if (!$this->verification_code || !$this->verification_code_expires_at) {
        return false;
    }

    if (now()->greaterThan($this->verification_code_expires_at)) {
        return false; // Code expired
    }

    if ($this->verification_code !== $code) {
        return false; // Code doesn't match
    }

    // Mark email as verified
    $this->email_verified_at = now();
    $this->verification_code = null;
    $this->verification_code_expires_at = null;
    $this->save();

    return true;
}

/**
 * Check if verification code is expired
 */
public function isVerificationCodeExpired(): bool
{
    if (!$this->verification_code_expires_at) {
        return true;
    }

    return now()->greaterThan($this->verification_code_expires_at);
}


}
