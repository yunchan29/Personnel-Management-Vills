<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show() {
        return $this->showProfileByRole('applicant');
    }

    public function edit() {
        return $this->editProfileByRole('applicant');
    }

    public function update(Request $request) {
        return $this->updateProfileByRole($request, 'applicant');
    }

    public function showEmployee() {
        return $this->showProfileByRole('employee');
    }

    public function editEmployee() {
        return $this->editProfileByRole('employee');
    }

    public function updateEmployee(Request $request) {
        return $this->updateProfileByRole($request, 'employee');
    }

    public function showHrAdmin() {
        return $this->showProfileByRole('hrAdmin');
    }

    public function editHrAdmin() {
        return $this->editProfileByRole('hrAdmin');
    }

    public function updateHrAdmin(Request $request) {
        return $this->updateProfileByRole($request, 'hrAdmin');
    }

    private function showProfileByRole($role) {
        $user = auth()->user();
        return view("$role.profile", compact('user'));
    }

    private function editProfileByRole($role) {
        $user = auth()->user();
        return view("$role.edit", compact('user'));
    }

    private function updateProfileByRole(Request $request, $role)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // 1. Validate everything (including the file, if any)
        $validated = $request->validate($this->validationRules($user->id));

        // 2. If there’s an uploaded picture, store it and inject into validated data
        if ($request->hasFile('profile_picture')) {
            $path = $request
                ->file('profile_picture')
                ->store('profile_pictures', 'public');
            Log::info('Profile picture uploaded:', ['path' => $path]);
            $validated['profile_picture'] = $path;
        }

        // 3. Log the exact array you’re about to save
        Log::info("Updating $role profile with data:", [
            'user_id' => $user->id,
            'data'    => $validated,
        ]);

        // 4. Mass‐assign and save
        $user->fill($validated);
        $user->save();

        return redirect()
            ->route("$role.profile")
            ->with('success', 'Profile updated successfully!');
    }


    private function validationRules($userId) {
        return [
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
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'mobile_number' => 'nullable|string|max:15',
            'full_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'street_details' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ];
    }

    public function changePassword(Request $request) {
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

    public function deleteAccount(Request $request) {
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
}
