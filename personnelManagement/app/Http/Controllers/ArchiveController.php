<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ArchiveController extends Controller
{
    public function index()
    {
        // Get archived applications (not users)
        $applications = Application::with(['user', 'job'])
            ->where('is_archived', true)
            ->get();

        return view('hrAdmin.archive', compact('applications'));
    }

   public function restore($id)
{
    $application = Application::findOrFail($id);

    // Restore only this application
    $application->update([
        'is_archived' => false,
        'status' => 'pending', // reset to pending
    ]);

    return redirect()->route('hrAdmin.archive.index')
        ->with('success', 'Application restored and set to pending.');
}

    public function destroy($id)
    {
        $application = Application::findOrFail($id);

        $application->delete(); // permanently delete application only

        return redirect()->route('hrAdmin.archive.index')
            ->with('success', 'Application permanently deleted.');
    }
}
