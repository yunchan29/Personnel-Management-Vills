<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\File201;
use App\Models\OtherFile;
use App\Models\Resume;
use App\Models\Application;

/**
 * ✅ SECURITY FIX: Secure file serving controller
 * Ensures files are served only to authorized users with proper authentication checks
 */
class SecureFileController extends Controller
{
    /**
     * Serve resume files securely
     */
    public function serveResume($filename)
    {
        $user = auth()->user();

        // Find the resume
        $resume = Resume::where('resume', 'like', "%{$filename}%")->first();

        if (!$resume) {
            abort(404, 'File not found.');
        }

        // Authorization check: User can only access their own resume OR HR staff can access applicant resumes
        $canAccess = false;

        if ($user->id === $resume->user_id) {
            $canAccess = true; // Own resume
        } elseif (in_array($user->role, ['hrAdmin', 'hrStaff'])) {
            // HR can access resumes of applicants with active applications
            $hasActiveApplication = Application::where('user_id', $resume->user_id)
                ->whereNotIn('status', ['declined', 'failed'])
                ->exists();
            $canAccess = $hasActiveApplication;
        }

        if (!$canAccess) {
            Log::warning('Unauthorized file access attempt', [
                'user_id' => $user->id,
                'role' => $user->role,
                'file' => $filename,
                'ip' => request()->ip()
            ]);
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $resume->resume);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Serve 201 files (government IDs) securely
     */
    public function serveOtherFile($filename)
    {
        $user = auth()->user();

        // Find the file
        $otherFile = OtherFile::where('file_path', 'like', "%{$filename}%")->first();

        if (!$otherFile) {
            abort(404, 'File not found.');
        }

        // Authorization check
        $canAccess = false;

        if ($user->id === $otherFile->user_id) {
            $canAccess = true; // Own file
        } elseif (in_array($user->role, ['hrAdmin', 'hrStaff'])) {
            // HR can access files of applicants with active applications
            $hasActiveApplication = Application::where('user_id', $otherFile->user_id)
                ->whereNotIn('status', ['declined', 'failed'])
                ->exists();
            $canAccess = $hasActiveApplication;
        }

        if (!$canAccess) {
            Log::warning('Unauthorized file access attempt', [
                'user_id' => $user->id,
                'role' => $user->role,
                'file' => $filename,
                'ip' => request()->ip()
            ]);
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $otherFile->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Serve profile pictures securely
     */
    public function serveProfilePicture($filename)
    {
        $user = auth()->user();

        // Profile pictures are less sensitive but still need auth
        // Users can view their own, HR can view anyone's, employees can view each other

        $filePath = storage_path('app/public/profile_pictures/' . $filename);

        if (!file_exists($filePath)) {
            // Return default image if not found
            $defaultPath = public_path('images/default.png');
            if (file_exists($defaultPath)) {
                return response()->file($defaultPath);
            }
            abort(404, 'File not found.');
        }

        // Verify ownership: Check if this picture belongs to a user in the system
        $pictureOwner = \App\Models\User::where('profile_picture', 'LIKE', '%' . $filename)->first();

        if (!$pictureOwner) {
            abort(403, 'Unauthorized access to this file.');
        }

        // Authorization check: User can view their own or HR can view anyone's
        if ($user->id !== $pictureOwner->id && !in_array($user->role, ['hrAdmin', 'hrStaff'])) {
            // For employees viewing each other's pictures (e.g., in employee directory)
            // Allow if both are employees (have role 'employee' or similar)
            // This can be adjusted based on your business rules
            if ($user->role !== 'employee' || $pictureOwner->role !== 'employee') {
                abort(403, 'Unauthorized. You can only view your own profile picture or HR can view all.');
            }
        }

        // Determine mime type
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Serve application resume snapshots securely
     */
    public function serveResumeSnapshot($filename)
    {
        $user = auth()->user();

        // Find application with this snapshot
        $application = Application::where('resume_snapshot', 'like', "%{$filename}%")->first();

        if (!$application) {
            abort(404, 'File not found.');
        }

        // Authorization check
        $canAccess = false;

        if ($user->id === $application->user_id) {
            $canAccess = true; // Own application
        } elseif (in_array($user->role, ['hrAdmin', 'hrStaff'])) {
            $canAccess = true; // HR can access all application snapshots
        }

        if (!$canAccess) {
            Log::warning('Unauthorized file access attempt', [
                'user_id' => $user->id,
                'role' => $user->role,
                'file' => $filename,
                'ip' => request()->ip()
            ]);
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $application->resume_snapshot);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
}
