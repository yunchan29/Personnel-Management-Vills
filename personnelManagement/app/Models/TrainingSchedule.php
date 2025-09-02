<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'remarks',
        'status',
        'scheduled_by',
        'scheduled_at'
    ];

    protected $casts = [
        'start_date' => 'date',    // ok lang, date talaga
        'end_date'   => 'date',
        'start_time' => 'string',  // oras lang, wag i-convert to Carbon
        'end_time'   => 'string',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduler()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}