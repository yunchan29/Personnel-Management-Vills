<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'sex' => 'nullable|string|max:10',
            'civil_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'suffix' => $request->suffix,
            'birthdate' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'age' => $request->age,
            'sex' => $request->sex,
            'civil_status' => $request->civil_status,
            'religion' => $request->religion,
            'nationality' => $request->nationality,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'full_address' => $request->full_address,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
        ]);

        return redirect()->route('applicant.profile')->with('success', 'Profile updated successfully!');
    }
}
