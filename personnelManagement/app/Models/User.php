<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use App\Models\Resume;

class User extends Authenticatable
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

        'full_address',
        'province',
        'city',
        'barangay',
        'uuid',
        'street_details',
        'postal_code',
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
           $this->profile_picture;
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

public function job()
{
    return $this->belongsTo(Job::class);
}


}
