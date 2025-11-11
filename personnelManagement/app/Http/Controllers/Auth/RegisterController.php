<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Traits\ApiResponseTrait;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    /**
     * Standardized password validation rules
     * Used consistently across registration and password changes
     */
    public static function getPasswordRules(bool $isConfirmed = true): array
    {
        $rules = [
            'required',
            'string',
            'min:8',
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            'regex:/[@$!%*#?&]/', // must contain a special character
        ];

        if ($isConfirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

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
            'password'     => self::getPasswordRules(),
            'terms' => ['accepted'],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                $validator->errors()->toArray(),
                'Validation failed.',
                $request->all()
            );
        }

        // Convert birth_date from m/d/Y to Y-m-d
        try {
            $birthDate = Carbon::createFromFormat('m/d/Y', $request->birth_date);
        } catch (\Exception $e) {
            return $this->errorResponse(
                ['birth_date' => ['Invalid birth date format.']],
                'Invalid birth date format.',
                $request->all()
            );
        }

        // Backend age enforcement
        if ($birthDate->age < 18) {
            return $this->errorResponse(
                ['birth_date' => ['You must be at least 18 years old to register.']],
                'You must be at least 18 years old to register.',
                $request->all()
            );
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

        // Generate verification code
        $code = $user->generateVerificationCode();

        // Send verification code via email
        $user->notify(new \App\Notifications\VerifyEmailCodeNotification($code));

        // Store email in session for verification page (user is NOT logged in)
        session(['verification_email' => $user->email]);

        return $this->successResponse(
            'Account created successfully! Please check your email for the verification code.',
            route('verification.notice'),
            [
                'user' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                ]
            ]
        );
    }
}
