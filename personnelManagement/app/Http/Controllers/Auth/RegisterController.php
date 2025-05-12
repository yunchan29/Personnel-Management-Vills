<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Add the 'role' field here, defaulting to 'applicant'
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'birth_date' => $request->birthdate,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => 'applicant', // Automatically assigning the 'applicant' role
        ]);

        auth()->login($user);

        // Redirect based on the user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'applicant') {
            return redirect()->route('applicant.dashboard');
        }

        // Fallback if no specific role match
        return redirect('/login');
    }
}
