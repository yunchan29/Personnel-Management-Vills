<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ContractInvitation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractSigningInvitationMail;

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
            'contract_signing_time' => [
                'required',
                'string',
                'regex:/^(1[0-2]|[1-9]):[0-5][0-9] (AM|PM)$/' // Format: H:MM AM/PM
            ],
        ], [
            'contract_signing_time.regex' => 'Invalid time format. Please use H:MM AM/PM format (e.g., 9:30 AM).'
        ]);

        // Combine date + time into one Carbon instance
        $date = $request->contract_date;              // e.g. 2025-10-05
        $time = $request->contract_signing_time;      // e.g. "9:15 AM"

        // Parse into a single datetime
        $schedule = Carbon::parse("{$date} {$time}");

        // Edge Case 1: Validate that the combined datetime is not in the past
        if ($schedule->isPast()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The scheduled date and time cannot be in the past.'
                ], 400);
            }
            return redirect()->back()->with('error', 'The scheduled date and time cannot be in the past.');
        }

        // Edge Case 2: Validate business hours (6 AM - 5 PM)
        $hour = $schedule->format('H');
        if ($hour < 6 || $hour >= 17) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM).'
                ], 400);
            }
            return redirect()->back()->with('error', 'Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM).');
        }

        $application = Application::with('evaluation')->findOrFail($applicationId);

        // Edge Case 3: Check if applicant is already hired
        if ($application->status->value === 'hired') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This applicant has already been hired. Cannot send invitation.'
                ], 400);
            }
            return redirect()->back()->with('error', 'This applicant has already been hired.');
        }

        // Edge Case 4: Check if applicant is archived
        if ($application->status->value === 'archived') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This applicant has been archived. Cannot send invitation.'
                ], 400);
            }
            return redirect()->back()->with('error', 'This applicant has been archived.');
        }

        // Edge Case 5: Ensure applicant passed evaluation
        if (!$application->evaluation || $application->evaluation->result !== 'Passed') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant must pass training evaluation before setting a contract signing schedule.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Applicant must pass training evaluation before setting a contract signing schedule.');
        }

        // Edge Case 6: Check for spam - limit invitations sent in last 24 hours
        $recentInvitationsCount = ContractInvitation::where('application_id', $application->id)
            ->where('sent_at', '>=', now()->subDay())
            ->count();

        if ($recentInvitationsCount >= 5) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many invitations sent recently. Please wait before sending another invitation.'
                ], 429);
            }
            return redirect()->back()->with('error', 'Too many invitations sent recently. Please wait before sending another invitation.');
        }

        // Edge Case 7: Check for duplicate invitation on the same exact date/time
        $duplicateInvitation = ContractInvitation::where('application_id', $application->id)
            ->where('contract_date', $date)
            ->where('contract_signing_time', $time)
            ->where('sent_at', '>=', now()->subHours(2))
            ->exists();

        if ($duplicateInvitation) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An invitation for this exact date and time was already sent recently.'
                ], 400);
            }
            return redirect()->back()->with('error', 'An invitation for this exact date and time was already sent recently.');
        }

        // Save schedule (assuming contract_signing_schedule is a datetime column in DB)
        $application->update([
            'contract_signing_schedule' => $schedule,
        ]);

        // Send email invitation to the applicant
        $emailSent = true;
        try {
            Mail::to($application->user->email)->send(new ContractSigningInvitationMail($application));
        } catch (\Exception $e) {
            \Log::error('Failed to send contract signing invitation email: ' . $e->getMessage());
            $emailSent = false;
        }

        // Create invitation record to track this send
        ContractInvitation::create([
            'application_id' => $application->id,
            'sent_by' => auth()->id(),
            'contract_date' => $date,
            'contract_signing_time' => $time,
            'email_sent' => $emailSent,
            'sent_at' => now(),
        ]);

        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $emailSent
                    ? 'Contract signing invitation sent successfully.'
                    : 'Schedule saved successfully. Note: Email notification could not be sent.',
                'email_sent' => $emailSent
            ], 200);
        }

        return redirect()->back()->with(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'Contract signing invitation sent successfully.'
                : 'Schedule saved successfully. Note: Email notification could not be sent.'
        );
    }





 /**
 * Store contract dates (start & end)
 */
public function storeDates(Request $request, $applicationId)
{
    $request->validate([
        'contract_start' => 'required|date|after_or_equal:today',
        'period'         => 'required|in:6m,1y', // make sure period is valid
    ]);

    $application = Application::findOrFail($applicationId);

    // Note: contract_signing_schedule is now optional
    // HR can set contract dates for direct promotions without requiring a signing ceremony

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

    // Check if it's an AJAX request
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Contract dates saved successfully.'
        ], 200);
    }

    return redirect()->back()->with('success', 'Contract dates saved successfully.');
}
}