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

        // Calculate age from birth_date
        $birthDate = Carbon::parse($request->birth_date);
        $age = $birthDate->age;

        // Create user with calculated age and default 'applicant' role
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'age' => $age,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => 'applicant',
        ]);

        auth()->login($user);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'applicant') {
            return redirect()->route('applicant.dashboard');
        }

        return redirect('/login');
    }
}
