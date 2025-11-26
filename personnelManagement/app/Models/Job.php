<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

   protected $fillable = [
    'job_title',
    'company_name',
    'job_industry',
    'location',
    'vacancies',
    'apply_until',
    'status',
    'qualifications',
    'additional_info',
];

    protected $casts = [
        'qualifications' => 'array',
        'additional_info' => 'array',
    ];

    /**
     * Scope a query to only include jobs that have not expired.
     */
    public function scopeNotExpired($query)
    {
        return $query->whereDate('apply_until', '>=', \Carbon\Carbon::today());
    }

public function applications()
{
    return $this->hasMany(Application::class, 'job_id');
}

public function employees()
{
    return $this->hasMany(User::class);
}

/**
 * Check if this job has available vacancies
 */
public function hasAvailableVacancies(): bool
{
    return $this->vacancies > 0;
}

/**
 * Get the number of remaining vacancies
 */
public function getRemainingVacancies(): int
{
    return max(0, $this->vacancies);
}

/**
 * Get all active (non-terminal, non-archived) applications for this job
 */
public function getActiveApplications()
{
    $terminalStatuses = \App\Enums\ApplicationStatus::terminalStatuses();

    return $this->applications()
        ->whereNotIn('status', array_map(fn($status) => $status->value, $terminalStatuses))
        ->where('is_archived', false);
}

/**
 * Reject all remaining active applicants for this job
 *
 * @param string $reason The reason for rejection (unused for now)
 * @return int Number of applicants rejected
 */
public function rejectRemainingApplicants(string $reason = 'Position filled'): int
{
    $activeApplications = $this->getActiveApplications()->get();

    foreach ($activeApplications as $application) {
        $application->setStatus(\App\Enums\ApplicationStatus::REJECTED);
        $application->save();
    }

    return $activeApplications->count();
}


}
