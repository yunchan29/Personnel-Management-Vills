<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    return view('users.leaveForm', compact('leaveForms'));
}


    public function store(Request $request)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'leave_type' => 'required|string',
            'date_range' => ['required', 'string', function ($attribute, $value, $fail) {
                // Validate date range format and logic
                // Expected format: "MM/DD/YYYY - MM/DD/YYYY" or similar
                $dates = explode(' - ', $value);

                if (count($dates) !== 2) {
                    $fail('The date range must contain a start and end date separated by " - ".');
                    return;
                }

                try {
                    $startDate = \Carbon\Carbon::parse(trim($dates[0]));
                    $endDate = \Carbon\Carbon::parse(trim($dates[1]));

                    // Validate start date is not in the past (allow today)
                    if ($startDate->lt(\Carbon\Carbon::today())) {
                        $fail('The leave start date cannot be in the past.');
                        return;
                    }

                    // Validate end date is after or equal to start date
                    if ($endDate->lt($startDate)) {
                        $fail('The leave end date must be after or equal to the start date.');
                        return;
                    }
                } catch (\Exception $e) {
                    $fail('The date range contains invalid dates.');
                }
            }],
            'about'      => 'nullable|string',
        ]);

        $path = $request->file('attachment')->store('leave_forms', 'public');

        LeaveForm::create([
            'user_id'    => Auth::id(),
            'leave_type' => $request->leave_type,
            'date_range' => $request->date_range,
            'about'      => $request->about,
            'file_path'  => $path,
            'status'     => 'Pending',
        ]);

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
            });

            return back()->with('success', 'Leave request approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage() ?: 'Failed to approve leave request. Please try again.');
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
            });

            return back()->with('success', 'Leave request declined successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage() ?: 'Failed to decline leave request. Please try again.');
        }
    }
}
