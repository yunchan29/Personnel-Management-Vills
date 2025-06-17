<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Show the profile of the authenticated applicant user.
     */
    public function show()
    {
        $user = auth()->user();
        return view('applicant.profile', compact('user'));
    }

    /**
     * Show the edit form for the authenticated applicant user's profile.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('applicant.edit', compact('user'));
    }

    /**
     * Update the authenticated applicant user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: No authenticated user.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10',
            'civil_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'street_details' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        ]);

        $data = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        Log::info('Updating applicant profile', [
            'user_id' => $user->id,
            'data' => $data,
        ]);

        $user->update($data);

        return redirect()->route('applicant.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the profile of an employee user.
     */
    public function showEmployee()
    {
        $user = auth()->user();
        return view('employee.profile', compact('user'));
    }

    /**
     * Show the edit form for an employee user's profile.
     */
    public function editEmployee()
    {
        $user = auth()->user();
        return view('employee.edit', compact('user'));
    }

    /**
     * Update the authenticated employee user's profile.
     */
    public function updateEmployee(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: No authenticated user.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10',
            'civil_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'street_details' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        ]);

        $data = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        Log::info('Updating employee profile', [
            'user_id' => $user->id,
            'data' => $data,
        ]);

        $user->update($data);

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'delete_password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->delete_password, $user->password)) {
            return back()->withErrors(['delete_password' => 'Incorrect password.']);
        }

        Auth::logout();

        $user->delete();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
        /**
     * Show the profile of an HR Admin user.
     */
    public function showHrAdmin()
    {
        $user = auth()->user();
        return view('hrAdmin.profile', compact('user'));
    }

    /**
     * Show the edit form for an HR Admin user's profile.
     */
    public function editHrAdmin()
    {
        $user = auth()->user();
        return view('hrAdmin.edit', compact('user'));
    }

    /**
     * Update the authenticated HR Admin user's profile.
     */
    public function updateHrAdmin(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: No authenticated user.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:10',
            'civil_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'street_details' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        ]);

        $data = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        Log::info('Updating HR Admin profile', [
            'user_id' => $user->id,
            'data' => $data,
        ]);

        $user->update($data);

        return redirect()->route('hrAdmin.profile')->with('success', 'Profile updated successfully!');
    }

}
