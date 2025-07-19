<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Interview extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'scheduled_by',
        'scheduled_at',
        'rescheduled_at',
        'status',
        'remarks'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}