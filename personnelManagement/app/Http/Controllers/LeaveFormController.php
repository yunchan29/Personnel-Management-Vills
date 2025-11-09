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
        // HR Admin and HR Staff can view all leave forms
        $leaveForms = LeaveForm::with('user')->latest()->get();

        return view('admins.shared.leaveForm', compact('leaveForms'));
    }

    // Regular employee view
    $leaveForms = LeaveForm::where('user_id', $user->id)->latest()->get();
    return view('users.leaveForm', compact('leaveForms'));
}


    public function store(Request $request)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'leave_type' => 'required|string',
            'date_range' => 'required|string',
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

        Storage::delete($form->file_path);
        $form->delete();

        return back()->with('success', 'Leave form deleted.');
    }

    public function approve($id)
    {
        // Verify user has HR role
        if (!in_array(Auth::user()->role, ['hrAdmin', 'hrStaff'])) {
            abort(403, 'Unauthorized. Only HR personnel can approve leave requests.');
        }

        $form = LeaveForm::findOrFail($id);
        $form->status = 'Approved';
        $form->save();

        return back()->with('success', 'Leave request approved.');
    }

    public function decline($id)
    {
        // Verify user has HR role
        if (!in_array(Auth::user()->role, ['hrAdmin', 'hrStaff'])) {
            abort(403, 'Unauthorized. Only HR personnel can decline leave requests.');
        }

        $form = LeaveForm::findOrFail($id);
        $form->status = 'Declined';
        $form->save();

        return back()->with('success', 'Leave request declined.');
    }
}
