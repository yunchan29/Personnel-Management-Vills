<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Resume;

class ResumeController extends Controller
{
    /**
     * Display the upload form and current resume.
     */
    public function show()
    {
        $resume = auth()->user()->resume;
        return view('applicant.application', compact('resume'));
    }

    /**
     * Store or overwrite the user's resume.
     */
    public function store(Request $request)
    {
        $request->validate([
            'resume_file' => 'required|file|mimes:pdf|max:25000', // 25 MB
        ]);

        $user = auth()->user();
        $file   = $request->file('resume_file');
        $path   = $file->store('resumes', 'public');

        // If a record exists, delete old file and update path
        if ($user->resume) {
            Storage::disk('public')->delete($user->resume->resume);
            $user->resume->update(['resume' => $path]);
        } else {
            // First-time upload
            $user->resume()->create(['resume' => $path]);
        }

        return redirect()
            ->route('applicant.application')
            ->with('success', 'Resume uploaded successfully.');
    }

    /**
     * Delete the user's resume record and file.
     */
    public function destroy()
    {
        $resume = auth()->user()->resume;
        if ($resume) {
            Storage::disk('public')->delete($resume->resume);
            $resume->delete();
        }

        return redirect()
            ->route('applicant.application')
            ->with('success', 'Resume deleted.');
    }
}