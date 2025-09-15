<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class StaffArchiveController extends Controller
{
    public function index()
    {
        // Show all manually archived applications (staff side)
        $applications = Application::with(['user', 'job'])
            ->where('is_archived', true)
            ->get();

        return view('hrStaff.archive', compact('applications'));
    }

    public function store($id)
    {
        $application = Application::findOrFail($id);

        $application->update([
            'is_archived' => true,
        ]);

        return redirect()->back()->with('success', 'Application archived successfully.');
    }

    public function restore($id)
    {
        $application = Application::findOrFail($id);

        $application->update([
            'is_archived' => false,
        ]);

        return redirect()->route('hrStaff.archive.index')
            ->with('success', 'Application restored successfully.');
    }

    public function destroy($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return redirect()->route('hrStaff.archive.index')
            ->with('success', 'Application permanently deleted.');
    }
}
