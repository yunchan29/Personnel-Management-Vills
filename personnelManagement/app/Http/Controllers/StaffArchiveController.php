<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class StaffArchiveController extends Controller
{
    public function index()
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can access archives.');
        }

        // Show all manually archived applications (staff side)
        $applications = Application::with(['user', 'job'])
            ->where('is_archived', true)
            ->get();

        return view('admins.shared.archive', compact('applications'));
    }

    public function store(Request $request, $id)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can archive applications.');
        }

        $application = Application::findOrFail($id);

        $application->update([
            'is_archived' => true,
        ]);

        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application archived successfully.'
            ], 200);
        }

        return redirect()->back()->with('success', 'Application archived successfully.');
    }

    public function restore($id)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can restore applications.');
        }

        $application = Application::findOrFail($id);

        $application->update([
            'is_archived' => false,
        ]);

        return redirect()->route('hrStaff.archive.index')
            ->with('success', 'Application restored successfully.');
    }

    public function destroy($id)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can delete applications.');
        }

        $application = Application::findOrFail($id);
        $application->delete();

        return redirect()->route('hrStaff.archive.index')
            ->with('success', 'Application permanently deleted.');
    }
}
