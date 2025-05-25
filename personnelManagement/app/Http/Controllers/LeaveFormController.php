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
        $leaveForms = LeaveForm::where('user_id', Auth::id())->get();
        return view('employee.leaveForm', compact('leaveForms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'leave_type' => 'required|string',
            'date_range' => 'required|string',
            'about' => 'nullable|string',
        ]);

        $path = $request->file('attachment')->store('leave_forms', 'public');

        LeaveForm::create([
            'user_id'     => Auth::id(),
            'leave_type'  => $request->leave_type,
            'date_range'  => $request->date_range,
            'about'       => $request->about,
            'file_path'   => $path,
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
}
