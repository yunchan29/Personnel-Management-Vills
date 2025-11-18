<?php

namespace App\Services;

use App\Models\File201;
use App\Models\OtherFile;

class RequirementsCheckService
{
    /**
     * Required documents list (same as HR Staff requirements modal)
     */
    const REQUIRED_DOCUMENTS = [
        'Barangay Clearance',
        'NBI Clearance',
        'Police Clearance',
        'Medical Certificate',
        'Birth Certificate'
    ];

    /**
     * Check if user has missing requirements
     *
     * @param int $userId
     * @return array ['has_missing' => bool, 'missing' => array, 'missing_count' => int]
     */
    public static function checkMissingRequirements(int $userId): array
    {
        $file201 = File201::where('user_id', $userId)->first();
        $otherFiles = OtherFile::where('user_id', $userId)->get();

        // Check missing documents
        $uploadedDocTypes = $otherFiles->pluck('type')->toArray();
        $missingDocs = collect(self::REQUIRED_DOCUMENTS)
            ->filter(fn($doc) => !in_array($doc, $uploadedDocTypes))
            ->values();

        // Check missing File201 fields (government IDs)
        $missingFile201 = collect([
            'SSS Number' => empty($file201?->sss_number),
            'PhilHealth Number' => empty($file201?->philhealth_number),
            'Pag-IBIG Number' => empty($file201?->pagibig_number),
            'TIN ID Number' => empty($file201?->tin_id_number),
        ])->filter(fn($missing) => $missing)->keys();

        // Merge both lists
        $allMissing = $missingFile201->merge($missingDocs)->values()->toArray();

        return [
            'has_missing' => count($allMissing) > 0,
            'missing' => $allMissing,
            'missing_count' => count($allMissing),
        ];
    }

    /**
     * Quick check if user has any missing requirements
     *
     * @param int $userId
     * @return bool
     */
    public static function hasMissingRequirements(int $userId): bool
    {
        $check = self::checkMissingRequirements($userId);
        return $check['has_missing'];
    }

    /**
     * Get formatted list of missing requirements for display
     *
     * @param int $userId
     * @return string
     */
    public static function getMissingRequirementsList(int $userId): string
    {
        $check = self::checkMissingRequirements($userId);

        if (!$check['has_missing']) {
            return 'All requirements complete';
        }

        return implode(', ', array_slice($check['missing'], 0, 3)) .
               (count($check['missing']) > 3 ? ', and more' : '');
    }
}
