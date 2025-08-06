<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'resume_snapshot',
        'licenses',
        'sss_number',
        'philhealth_number',
        'tin_id_number',
        'pagibig_number',
        'status',
    ];

    protected $casts = [
        'licenses' => 'array',
        'interview_schedule' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getResumeSnapshotUrlAttribute()
    {
        return $this->resume_snapshot ? \Storage::url($this->resume_snapshot) : null;
    }

    public function interview()
    {
        return $this->hasOne(Interview::class);
    }

    public function trainingSchedule()
    {
        return $this->hasOne(TrainingSchedule::class);
    }
    public function evaluation()
{
    return $this->hasOne(\App\Models\TrainingEvaluation::class);
}

}
