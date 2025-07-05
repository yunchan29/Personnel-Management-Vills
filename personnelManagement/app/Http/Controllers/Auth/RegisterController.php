<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // Custom password validation rule
        $validator = Validator::make($request->all(), [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birth_date'   => ['required', 'date_format:m/d/Y'],
            'gender'       => ['required', 'string'],
            'password'     => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('The password must contain at least one uppercase letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('The password must contain at least one number.');
                    }
                    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                        $fail('The password must contain at least one special character.');
                    }
                },
            ],
            'terms' => ['accepted'],
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

        // Backend age enforcement
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
            'birth_date' => $birthDate->format('Y-m-d'),
            'age'        => $birthDate->age,
            'gender'     => $request->gender,
            'password'   => Hash::make($request->password),
            'role'       => 'applicant',
        ]);

        auth()->login($user);

        // Redirect based on role with SweetAlert success
        $successMessage = 'Welcome! Your account has been successfully created.';

        return match ($user->role) {
            'admin'     => redirect()->route('admin.dashboard')->with('success', $successMessage),
            'applicant' => redirect()->route('applicant.dashboard')->with('success', $successMessage),
            default     => redirect('/login')->with('success', $successMessage),
        };
    }
}
