<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\ApplicationStatus;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'resume_snapshot',
        'contract_signing_schedule',
        'contract_start',
        'contract_end',
        'status',        // Allow HR Staff to update status
        'is_archived',   // Allow HR Staff to archive/unarchive
    ];

    protected $casts = [
        'interview_schedule' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_archived' => 'boolean', // ✅ for archiving
        'contract_signing_schedule' => 'datetime',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'status' => ApplicationStatus::class, // Cast to enum
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

    public function contractInvitations()
    {
        return $this->hasMany(\App\Models\ContractInvitation::class);
    }

    /**
     * Set application status (accepts both enum and string)
     */
    public function setStatus(ApplicationStatus|string $status): void
    {
        if (is_string($status)) {
            $status = ApplicationStatus::fromString($status);
        }

        if ($status) {
            $this->status = $status;
        }
    }

    /**
     * Check if application needs approval/decline
     */
    public function needsApproval(): bool
    {
        return in_array($this->status, ApplicationStatus::needsApproval());
    }

    /**
     * Check if application is in interview stage
     */
    public function isInInterview(): bool
    {
        return in_array($this->status, ApplicationStatus::interviewStatuses());
    }

    /**
     * Check if application is in training stage
     */
    public function isInTraining(): bool
    {
        return in_array($this->status, ApplicationStatus::trainingStatuses());
    }

    /**
     * Check if application is ready for evaluation
     */
    public function isReadyForEvaluation(): bool
    {
        return in_array($this->status, ApplicationStatus::evaluationStatuses());
    }

    /**
     * Check if application process is complete (terminal status)
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, ApplicationStatus::terminalStatuses());
    }

    /**
     * Get status badge HTML class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status ? $this->status->badgeClass() : 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? $this->status->label() : 'Unknown';
    }
}





