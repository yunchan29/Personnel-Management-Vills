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
            $application->update([
                'contract_signing_schedule' => null,
                'contract_start' => null,  // also reset contract dates if schedule is deleted
                'contract_end'   => null,
            ]);
            return redirect()->back()->with('success', 'Contract signing schedule removed.');
        }

        return redirect()->back()->with('error', 'No contract schedule found for this applicant.');
    }

    /**
     * Store contract dates (start & end)
     */
    public function storeDates(Request $request, $applicationId)
    {
        $request->validate([
            'contract_start' => 'required|date',
            'contract_end'   => 'required|date|after_or_equal:contract_start',
        ]);

        $application = Application::findOrFail($applicationId);

        // Ensure contract signing schedule exists before saving dates
        if (!$application->contract_signing_schedule) {
            return redirect()->back()->with('error', 'You must set a contract signing schedule before assigning contract dates.');
        }

        // Save contract dates
        $application->update([
            'contract_start' => $request->contract_start,
            'contract_end'   => $request->contract_end,
        ]);

        return redirect()->back()->with('success', 'Contract dates saved successfully.');
    }

    /**
     * Delete contract start & end dates
     */
    public function destroyDates($applicationId)
    {
        $application = Application::findOrFail($applicationId);

        if ($application->contract_start || $application->contract_end) {
            $application->update([
                'contract_start' => null,
                'contract_end'   => null,
            ]);

            return redirect()->back()->with('success', 'Contract dates removed successfully.');
        }

        return redirect()->back()->with('error', 'No contract dates found for this applicant.');
    }
}