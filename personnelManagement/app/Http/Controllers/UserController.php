<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Show the profile of the authenticated user.
     */
    public function show()
    {
        $user = auth()->user();
        return view('applicant.profile', compact('user'));
    }

    /**
     * Show the edit form for the authenticated user's profile.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('applicant.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: No authenticated user.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'gender' => 'nullable|string|max:10',

            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'age' => 'nullable|integer',

            'civil_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',

            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        $data = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        // Log data for debugging
        Log::info('Updating user profile', [
            'user_id' => $user->id,
            'data' => $data
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
     * Show the edit form for an employee profile.
     */
    public function editEmployee()
    {
        $user = auth()->user();
        return view('employee.edit', compact('user'));
    }

    /**
     * Update the employee user's profile.
     */
    public function updateEmployee(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: No authenticated user.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            // Additional validations if needed
        ]);

        Log::info('Updating employee profile', [
            'user_id' => $user->id,
            'data' => $validated
        ]);

        $user->update($validated);

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully!');
    }
}
