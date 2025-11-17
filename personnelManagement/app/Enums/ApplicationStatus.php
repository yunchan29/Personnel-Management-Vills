<?php

namespace App\Enums;

/**
 * Application Status Enum
 *
 * Defines all possible statuses for an application throughout the hiring process.
 * This provides a single source of truth for status values across the entire application.
 */
enum ApplicationStatus: string
{
    // Initial Application Statuses
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';

    // Interview Statuses
    case FOR_INTERVIEW = 'for_interview';
    case INTERVIEWED = 'interviewed';
    case FAILED_INTERVIEW = 'failed_interview';

    // Training Statuses
    case SCHEDULED_FOR_TRAINING = 'scheduled_for_training';
    case TRAINED = 'trained';

    // Evaluation Statuses
    case FOR_EVALUATION = 'for_evaluation';
    case PASSED_EVALUATION = 'passed_evaluation';
    case FAILED_EVALUATION = 'failed_evaluation';

    // Final Statuses
    case HIRED = 'hired';
    case REJECTED = 'rejected';

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
            self::FOR_INTERVIEW => 'For Interview',
            self::INTERVIEWED => 'Interviewed',
            self::FAILED_INTERVIEW => 'Failed Interview',
            self::SCHEDULED_FOR_TRAINING => 'Scheduled for Training',
            self::TRAINED => 'Trained',
            self::FOR_EVALUATION => 'For Evaluation',
            self::PASSED_EVALUATION => 'Passed Evaluation',
            self::FAILED_EVALUATION => 'Failed Evaluation',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Get CSS class for badge styling
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-gray-100 text-gray-800',
            self::APPROVED => 'bg-green-100 text-green-800',
            self::DECLINED, self::REJECTED => 'bg-red-100 text-red-800',
            self::FOR_INTERVIEW => 'bg-yellow-100 text-yellow-800',
            self::INTERVIEWED => 'bg-blue-100 text-blue-800',
            self::FAILED_INTERVIEW, self::FAILED_EVALUATION => 'bg-red-100 text-red-800',
            self::SCHEDULED_FOR_TRAINING => 'bg-blue-100 text-blue-800',
            self::TRAINED, self::FOR_EVALUATION => 'bg-purple-100 text-purple-800',
            self::PASSED_EVALUATION => 'bg-green-100 text-green-800',
            self::HIRED => 'bg-green-100 text-green-800',
        };
    }

    /**
     * Get all statuses that should be shown in applicants tab (needs approval)
     */
    public static function needsApproval(): array
    {
        return [
            self::PENDING,
        ];
    }

    /**
     * Get all statuses that should be shown in interview tab
     */
    public static function interviewStatuses(): array
    {
        return [
            self::APPROVED,
            self::FOR_INTERVIEW,
        ];
    }

    /**
     * Get all statuses that should be shown in training schedule tab
     */
    public static function trainingStatuses(): array
    {
        return [
            self::INTERVIEWED,
            self::SCHEDULED_FOR_TRAINING,
        ];
    }

    /**
     * Get all statuses that should be shown in evaluation tab
     * Includes SCHEDULED_FOR_TRAINING since training completion may not be explicitly marked
     */
    public static function evaluationStatuses(): array
    {
        return [
            self::TRAINED,
            self::FOR_EVALUATION,
            self::SCHEDULED_FOR_TRAINING, // Include for applicants who have training scheduled
            self::PASSED_EVALUATION, // Include applicants who passed evaluation
        ];
    }

    /**
     * Get all terminal/final statuses (application process complete)
     */
    public static function terminalStatuses(): array
    {
        return [
            self::DECLINED,
            self::FAILED_INTERVIEW,
            self::FAILED_EVALUATION,
            self::HIRED,
            self::REJECTED,
        ];
    }

    /**
     * Check if this status represents a failed/rejected state
     */
    public function isFailed(): bool
    {
        return in_array($this, [
            self::DECLINED,
            self::FAILED_INTERVIEW,
            self::FAILED_EVALUATION,
            self::REJECTED,
        ]);
    }

    /**
     * Check if this status represents a successful/passed state
     */
    public function isSuccessful(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::INTERVIEWED,
            self::TRAINED,
            self::PASSED_EVALUATION,
            self::HIRED,
        ]);
    }

    /**
     * Get the next logical status after a successful step
     */
    public function nextStatus(): ?self
    {
        return match($this) {
            self::PENDING => self::APPROVED,
            self::APPROVED, self::FOR_INTERVIEW => self::INTERVIEWED,
            self::INTERVIEWED, self::SCHEDULED_FOR_TRAINING => self::TRAINED,
            self::TRAINED, self::FOR_EVALUATION => self::PASSED_EVALUATION,
            self::PASSED_EVALUATION => self::HIRED,
            default => null,
        };
    }

    /**
     * Convert string to enum (case-insensitive)
     */
    public static function fromString(string $status): ?self
    {
        $status = strtolower(str_replace(' ', '_', $status));

        return match($status) {
            'pending' => self::PENDING,
            'approved' => self::APPROVED,
            'declined' => self::DECLINED,
            'for_interview' => self::FOR_INTERVIEW,
            'interviewed' => self::INTERVIEWED,
            'failed_interview', 'fail_interview' => self::FAILED_INTERVIEW,
            'scheduled_for_training' => self::SCHEDULED_FOR_TRAINING,
            'trained' => self::TRAINED,
            'for_evaluation' => self::FOR_EVALUATION,
            'passed_evaluation', 'passed', 'pass_evaluation' => self::PASSED_EVALUATION,
            'failed_evaluation', 'failed', 'fail_evaluation' => self::FAILED_EVALUATION,
            'hired' => self::HIRED,
            'rejected' => self::REJECTED,
            default => null,
        };
    }

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum values with labels
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
