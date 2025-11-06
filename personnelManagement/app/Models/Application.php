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
        'contract_signing_schedule',
        'contract_start',
        'contract_end',
    ];

    /**
     * The attributes that are guarded from mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'status',       // Prevent unauthorized status changes
        'is_archived',  // Prevent unauthorized archiving
    ];

    protected $casts = [
        'licenses' => 'array', 
        'interview_schedule' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_archived' => 'boolean', // ✅ for archiving
        'contract_signing_schedule' => 'datetime',
        'contract_start' => 'date',   
        'contract_end' => 'date',   
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



