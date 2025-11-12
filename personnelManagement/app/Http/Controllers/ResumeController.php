<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Resume;


class ResumeController extends Controller
{
    /**
     * Show the resume and myapplication page.
     */
    public function show()
    {
        $user = auth()->user();

        $resume = $this->getResume($user->id);
        $applications = $this->getApplications($user->id);

        return view('users.application', compact('resume', 'applications'));
    }


    /**
     * Store or update the resume file.
     */
   public function store(Request $request)
{
    $request->validate([
        'resume_file' => 'required|file|mimes:pdf|max:5120', // Reduced from 25MB to 5MB for security
    ]);

    $user = auth()->user();
    $file = $request->file('resume_file');

    // Additional security: Verify file is actually a PDF by checking magic bytes
    $fileContents = file_get_contents($file->getRealPath());
    if (substr($fileContents, 0, 4) !== '%PDF') {
        return redirect()
            ->back()
            ->withErrors(['resume_file' => 'Invalid PDF file detected. Please upload a valid PDF.']);
    }

    // Use random filename for better security
    $randomName = \Str::random(40) . '.pdf';
    $path = $file->storeAs('resumes', $randomName, 'public');

    if ($user->resume) {
        // Delete old file from storage
        Storage::disk('public')->delete($user->resume->resume);

        // Update DB
        $user->resume->update([
            'resume' => $path,
        ]);
    } else {
        $user->resume()->create([
            'resume' => $path,
        ]);
    }

    return redirect()
        ->route('applicant.application')
        ->with('success', 'Resume uploaded successfully!');
}

    /**
     * Delete resume and database reference.
     */
    public function destroy()
    {
        $resume = auth()->user()->resume;

        if ($resume) {
            // Delete file from storage
            Storage::disk('public')->delete($resume->resume);

            // Delete DB record
            $resume->delete();
        }

        return redirect()
            ->route('applicant.application')
            ->with('success', 'Resume deleted successfully!');
    }

    /**
     * Fetch resume of the applicant.
     */

    protected function getResume($userId)
    {
        return \App\Models\User::findOrFail($userId)->resume;
    }

    /**
     * Fetch applications of the applicant.
     */
        protected function getApplications($userId)
    {
        return Application::with('job')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Delete a specific application.
     */

    public function deleteApplication($id)
    {
        $application = Application::where('user_id', auth()->id())->findOrFail($id);

        // State validation: Only allow deletion of applications in certain states
        $deletableStates = ['pending', 'failed', 'rejected'];

        if (!in_array($application->status, $deletableStates)) {
            return back()->with('error',
                "Cannot delete this application. Applications can only be deleted when in 'Pending', 'Failed', or 'Rejected' status. Current status: {$application->status}");
        }

        try {
            \DB::transaction(function () use ($application) {
                // Delete the resume snapshot file if it exists
                if ($application->resume_snapshot && \Storage::disk('public')->exists($application->resume_snapshot)) {
                    \Storage::disk('public')->delete($application->resume_snapshot);
                }

                // Delete the application
                $application->delete();
            });

            return back()->with('success', 'Application deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete application: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete application. Please try again.');
        }
    }
}
