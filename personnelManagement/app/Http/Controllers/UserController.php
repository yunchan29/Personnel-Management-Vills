<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkExperience;
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

    public function toggleVisibility(Request $request)
    {
        $request->validate([
            'active_status' => 'required|in:Active,Inactive',
        ]);

        $user = auth()->user();
        $user->active_status = $request->input('active_status');
        $user->save();

        return redirect()->back()->with('success', 'Account status updated.');
    }

    private function showProfileByRole($role) {
        $user = auth()->user();
        $experiences = $user->workExperiences()->get();

        return view("$role.profile", compact('user', 'experiences'));
    }

    private function editProfileByRole($role) {
        $user = auth()->user();
        $experiences = $user->workExperiences()->get();

        return view("$role.profile", compact('user', 'experiences'));
    }

    private function updateProfileByRole(Request $request, $role)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate(array_merge(
            $this->validationRules($user->id),
            [
                'job_industry' => 'nullable|string|max:255',
                'work_experience.*.job_title' => 'nullable|string|max:255',
                'work_experience.*.company_name' => 'nullable|string|max:255',
                'work_experience.*.start_date' => 'nullable|date',
                'work_experience.*.end_date' => 'nullable|date|after_or_equal:work_experience.*.start_date',
            ]
        ));

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            Log::info('Profile picture uploaded:', ['path' => $path]);
            $validated['profile_picture'] = $path;
        }

        Log::info("Updating $role profile with data:", [
            'user_id' => $user->id,
            'data'    => $validated,
        ]);

        $user->fill($validated);
        $user->job_industry = $request->input('job_industry');
        $user->save();

        if ($request->has('work_experience')) {
            WorkExperience::where('user_id', $user->id)->delete();

            foreach ($request->input('work_experience') as $exp) {
                $isEmpty = empty($exp['job_title']) &&
                        empty($exp['company_name']) &&
                        empty($exp['start_date']) &&
                        empty($exp['end_date']);

                if ($isEmpty) {
                    continue; // Skip empty experience blocks
                }

                WorkExperience::create([
                    'user_id' => $user->id,
                    'job_title' => $exp['job_title'],
                    'company_name' => $exp['company_name'],
                    'start_date' => $exp['start_date'],
                    'end_date' => $exp['end_date'],
                ]);
            }
        }

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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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

    public function showHrStaff() {
    return $this->showProfileByRole('hrStaff');
}


}
