<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\LeaveApproveMail;
use App\Mail\LeaveDeclineMail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewLeaveRequestNotification;
use App\Notifications\LeaveStatusNotification;

class LeaveFormController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if (in_array($user->role, ['hrAdmin', 'hrStaff'])) {
        // HR Admin and HR Staff can view all leave forms with pagination
        $leaveForms = LeaveForm::with(['user.applications.job'])->latest()->paginate(25);

        return view('admins.shared.leaveForm', compact('leaveForms'));
    }

    // Regular employee view with pagination
    $leaveForms = LeaveForm::where('user_id', $user->id)->latest()->paginate(15);

    // Get the employee's contract dates from their latest application with contract dates
    // We check for applications that have both contract_start and contract_end
    $latestApplication = $user->applications()
        ->whereNotNull('contract_start')
        ->whereNotNull('contract_end')
        ->latest()
        ->first(['contract_start', 'contract_end']);

    $contractStart = $latestApplication?->contract_start;
    $contractEnd = $latestApplication?->contract_end;

    return view('users.leaveForm', compact('leaveForms', 'contractStart', 'contractEnd'));
}


    public function store(Request $request)
{
    $request->validate([
        'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'leave_type' => ['required', 'string', function ($attribute, $value, $fail) {

            $user = Auth::user();

            // Fetch latest contract
            $latestApplication = $user->applications()
                ->whereNotNull('contract_start')
                ->whereNotNull('contract_end')
                ->latest()
                ->first(['contract_start', 'contract_end']);

            // If no contract found, block all benefit leaves
            if (!$latestApplication) {
                if (in_array($value, [
                    'Vacation Leave',
                    'Emergency Leave',
                    'Maternity Leave',
                    'Paternity Leave',
                    'Special Leave'
                ])) {
                    $fail('You are not eligible for this leave. No valid contract found.');
                }
                return;
            }

            // Calculate contract duration
            $contractStart = \Carbon\Carbon::parse($latestApplication->contract_start);
            $contractEnd   = \Carbon\Carbon::parse($latestApplication->contract_end);
            $contractDurationInMonths = $contractStart->diffInMonths($contractEnd);

            /* -----------------------------------------
               SPECIAL LEAVE VALIDATION (Existing Logic)
            ------------------------------------------*/
            if ($value === 'Special Leave') {

                if ($contractDurationInMonths < 12) {
                    $fail('You are not eligible for Special Leave. It requires a contract duration of at least 1 year (12 months). Your contract duration is '
                        . $contractDurationInMonths . ' months.');
                }
                return;
            }

            /* ------------------------------------------------------
               🔶 BENEFIT LEAVES VALIDATION (NEW)
               Applies to: Vacation, Emergency, Maternity, Paternity
            -------------------------------------------------------*/
            $benefitLeaves = [
                'Vacation Leave',
                'Emergency Leave',
                'Maternity Leave',
                'Paternity Leave',
            ];

            if (in_array($value, $benefitLeaves)) {

                if ($contractDurationInMonths < 12) {
                    $fail('Your contract is only ' . $contractDurationInMonths . ' months. This leave type requires at least 1 year contract duration to access these benefit leaves.');
                }
            }

        }],

                'date_range' => ['required', 'string', function ($attribute, $value, $fail) {

                    // Validate date range format
                    $dates = explode(' - ', $value);

                    if (count($dates) !== 2) {
                        $fail('The date range must contain a start and end date separated by " - ".');
                        return;
                    }

                    try {
                        $startDate = \Carbon\Carbon::parse(trim($dates[0]));
                        $endDate   = \Carbon\Carbon::parse(trim($dates[1]));

                        // Start date cannot be in the past
                        if ($startDate->lt(\Carbon\Carbon::today())) {
                            $fail('The leave start date cannot be in the past.');
                            return;
                        }

                        // End date must be >= start
                        if ($endDate->lt($startDate)) {
                            $fail('The leave end date must be after or equal to the start date.');
                            return;
                        }

                    } catch (\Exception $e) {
                        $fail('The date range contains invalid dates.');
                    }

                }],

                'about' => 'nullable|string',
            ]);

            // Store file
            $path = $request->file('attachment')->store('leave_forms', 'public');

            // Insert leave form record
            $leaveForm = LeaveForm::create([
                'user_id'    => Auth::id(),
                'leave_type' => $request->leave_type,
                'date_range' => $request->date_range,
                'about'      => $request->about,
                'file_path'  => $path,
                'status'     => 'Pending',
            ]);

            // Notify HR
            $employee = Auth::user();
            $hrUsers = User::whereIn('role', ['hrAdmin', 'hrStaff'])->get();

            foreach ($hrUsers as $hrUser) {
                $hrUser->notify(new NewLeaveRequestNotification($leaveForm, $employee));
            }

            // Return JSON for Ajax
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave form submitted successfully.'
                ]);
            }

            return back()->with('success', 'Leave form submitted successfully.');
        }

    public function destroy($id)
    {
        $form = LeaveForm::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Secure file deletion: Check existence before deletion
        if ($form->file_path && Storage::disk('public')->exists($form->file_path)) {
            Storage::disk('public')->delete($form->file_path);
        }

        $form->delete();

        return back()->with('success', 'Leave form deleted.');
    }

    public function approve($id)
    {
        try {
            // Verify user has HR role
            if (!in_array(Auth::user()->role, ['hrAdmin', 'hrStaff'])) {
                abort(403, 'Unauthorized. Only HR personnel can approve leave requests.');
            }

            // Use transaction with pessimistic locking to prevent race conditions
            \DB::transaction(function () use ($id) {
                $form = LeaveForm::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($form->status !== 'Pending') {
                    throw new \Exception('This leave request has already been processed.');
                }

                $form->status = 'Approved';
                $form->save();

                // 🔔 Send email to employee
                Mail::to($form->user->email)->send(new LeaveApproveMail($form));

                // 🔔 Send in-app notification to employee
                $form->user->notify(new LeaveStatusNotification($form, 'approved'));
            });

             return back()->with('success', 'Leave request approved and email sent.');
            } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
            }
        }

    public function decline($id)
    {
        try {
            // Verify user has HR role
            if (!in_array(Auth::user()->role, ['hrAdmin', 'hrStaff'])) {
                abort(403, 'Unauthorized. Only HR personnel can decline leave requests.');
            }

            // Use transaction with pessimistic locking to prevent race conditions
            \DB::transaction(function () use ($id) {
                $form = LeaveForm::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($form->status !== 'Pending') {
                    throw new \Exception('This leave request has already been processed.');
                }

                $form->status = 'Declined';
                $form->save();

                // 🔔 Send email to employee
                Mail::to($form->user->email)->send(new LeaveDeclineMail($form));

                // 🔔 Send in-app notification to employee
                $form->user->notify(new LeaveStatusNotification($form, 'declined'));
        });

        return back()->with('success', 'Leave request declined and email sent.');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
    }
}
