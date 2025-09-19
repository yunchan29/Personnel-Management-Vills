<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ContractScheduleController extends Controller
{
    /**
     * Store or update contract signing schedule
     */
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'contract_signing_schedule' => 'required|date|after_or_equal:tomorrow',
        ]);

        $application = Application::with('evaluation')->findOrFail($applicationId);

        // Ensure applicant passed evaluation
        if (!$application->evaluation || $application->evaluation->result !== 'passed') {
            return redirect()->back()->with('error', 'Applicant must pass training evaluation before setting a contract signing schedule.');
        }

        // Save schedule
        $application->update([
            'contract_signing_schedule' => $request->contract_signing_schedule,
        ]);

        return redirect()->back()->with('success', 'Contract signing schedule set successfully.');
    }

    /**
     * Delete a contract signing schedule
     */
    public function destroy($applicationId)
    {
        $application = Application::findOrFail($applicationId);

        if ($application->contract_signing_schedule) {
            $application->update(['contract_signing_schedule' => null]);
            return redirect()->back()->with('success', 'Contract signing schedule removed.');
        }

        return redirect()->back()->with('error', 'No contract schedule found for this applicant.');
    }
}
