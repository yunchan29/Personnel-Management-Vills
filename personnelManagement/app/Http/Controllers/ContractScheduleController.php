<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractScheduleController extends Controller
{
    /**
     * Store or update contract signing schedule
     */
    public function store(Request $request, $applicationId)
    {
        // Validate incoming fields separately
        $request->validate([
            'contract_date' => 'required|date|after_or_equal:tomorrow',
            'contract_signing_time' => 'required|string',
        ]);

        // Combine date + time into one Carbon instance
        $date = $request->contract_date;              // e.g. 2025-10-05
        $time = $request->contract_signing_time;      // e.g. "9:15 AM"

        // Parse into a single datetime
        $schedule = Carbon::parse("{$date} {$time}");

        $application = Application::with('evaluation')->findOrFail($applicationId);

        // Ensure applicant passed evaluation
        if (!$application->evaluation || $application->evaluation->result !== 'Passed') {
            return redirect()->back()->with('error', 'Applicant must pass training evaluation before setting a contract signing schedule.');
        }

        // Save schedule (assuming contract_signing_schedule is a datetime column in DB)
        $application->update([
            'contract_signing_schedule' => $schedule,
        ]);

        

        return redirect()->back()->with('success', 'Contract signing schedule set successfully.');
    }





 /**
 * Store contract dates (start & end)
 */
public function storeDates(Request $request, $applicationId)
{
    $request->validate([
        'contract_start' => 'required|date',
        'period'         => 'required|in:6m,1y', // make sure period is valid
    ]);

    $application = Application::findOrFail($applicationId);

    // Ensure contract signing schedule exists before saving dates
    if (!$application->contract_signing_schedule) {
        return redirect()->back()->with('error', 'You must set a contract signing schedule before assigning contract dates.');
    }

    // Calculate end date from start + period
    $startDate = \Carbon\Carbon::parse($request->contract_start);
    $endDate = $startDate->copy();

    if ($request->period === '6m') {
        $endDate->addMonths(6);
    } elseif ($request->period === '1y') {
        $endDate->addYear();
    }

    // Save contract dates
    $application->update([
        'contract_start' => $startDate->format('Y-m-d'),
        'contract_end'   => $endDate->format('Y-m-d'),
    ]);

    return redirect()->back()->with('success', 'Contract dates saved successfully.');
}
}