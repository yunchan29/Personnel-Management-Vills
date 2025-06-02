<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon; // for date calculations

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birth_date'   => ['required', 'date_format:m/d/Y'],
            'gender'       => ['required', 'string'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'terms'        => ['accepted'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Convert birth_date from m/d/Y to Y-m-d
        try {
            $birthDate = Carbon::createFromFormat('m/d/Y', $request->birth_date);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['birth_date' => 'Invalid birth date format.'])
                ->withInput();
        }

        // Optional: Enforce 18+ rule on backend
        if ($birthDate->age < 18) {
            return redirect()->back()
                ->withErrors(['birth_date' => 'You must be at least 18 years old to register.'])
                ->withInput();
        }

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'birth_date' => $birthDate->format('Y-m-d'), // MySQL format
            'age'        => $birthDate->age,
            'gender'     => $request->gender,
            'password'   => Hash::make($request->password),
            'role'       => 'applicant', // default role
        ]);

        auth()->login($user);

        // Redirect by role
        return match ($user->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'applicant' => redirect()->route('applicant.dashboard'),
            default     => redirect('/login'),
        };
    }
}
