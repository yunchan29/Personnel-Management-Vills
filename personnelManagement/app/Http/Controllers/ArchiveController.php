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

        // Restore to previous status if available, otherwise fallback to "Pending"
        $application->update([
            'is_archived' => false,
            'status' => $application->previous_status ?? 'Pending',
        ]);

        return redirect()->route('hrAdmin.archive.index')
            ->with('success', 'Application restored to its previous status.');
    }

    public function destroy($id)
    {
        $application = Application::findOrFail($id);

        $application->delete(); // permanently delete application only

        return redirect()->route('hrAdmin.archive.index')
            ->with('success', 'Application permanently deleted.');
    }
}
