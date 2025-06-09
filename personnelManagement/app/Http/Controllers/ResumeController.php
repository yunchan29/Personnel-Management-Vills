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

        $view = $user->role === 'employee'
            ? 'employee.application'
            : 'applicant.application';

        return view($view, compact('resume', 'applications'));
    }


    /**
     * Store or update the resume file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'resume_file' => 'required|file|mimes:pdf|max:25000', // Max 25 MB
        ]);

        $user = auth()->user();
        $file = $request->file('resume_file');
        $path = $file->store('resumes', 'public');

        if ($user->resume) {
            // Delete old file
            Storage::disk('public')->delete($user->resume->resume);

            // Update existing record with new path
            $user->resume->update([
                'resume' => $path
            ]);
        } else {
            // First time upload
            $user->resume()->create([
                'resume' => $path
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
     * SFetch resume of the applicant.
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
        $application->delete();

        return back()->with('success', 'Application deleted successfully.');
    }
}
