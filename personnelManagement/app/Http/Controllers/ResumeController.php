<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Resume;

class ResumeController extends Controller
{
    /**
     * Show the resume upload page.
     */
 public function show()
{
    $resume = auth()->user()->resume;

    // Check if user is an applicant or employee
    $view = auth()->user()->role === 'employee'
        ? 'employee.application'
        : 'applicant.application';

    return view($view, compact('resume'));
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
}
