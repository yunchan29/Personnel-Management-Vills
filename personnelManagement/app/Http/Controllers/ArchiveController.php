<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    public function index()
    {
        // Verify user has HR Admin role
        if (Auth::user()->role !== 'hrAdmin') {
            abort(403, 'Unauthorized. Only HR Admin can access archives.');
        }

        // Get archived applications (not users)
        $applications = Application::with(['user', 'job'])
            ->where('is_archived', true)
            ->get();

        return view('admins.shared.archive', compact('applications'));
    }

    public function restore($id)
    {
        // Verify user has HR Admin role
        if (Auth::user()->role !== 'hrAdmin') {
            abort(403, 'Unauthorized. Only HR Admin can restore applications.');
        }

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
        // Verify user has HR Admin role
        if (Auth::user()->role !== 'hrAdmin') {
            abort(403, 'Unauthorized. Only HR Admin can delete applications.');
        }

        $application = Application::findOrFail($id);

        $application->delete(); // permanently delete application only

        return redirect()->route('hrAdmin.archive.index')
            ->with('success', 'Application permanently deleted.');
    }
}
