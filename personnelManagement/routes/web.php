<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Route for homepage (welcome page)
Route::get('/', function () {
    return view('welcome');
});

// Layout previews (optional)
Route::get('/layouts/applicantHome', function () {
    return view('layouts.applicantHome');
})->name('applicantHome');

Route::get('/layouts/employeeHome', function () {
    return view('layouts.employeeHome');
})->name('employeeHome');

// ✅ Applicant-related routes with auth middleware
Route::prefix('applicant')->name('applicant.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('applicant.dashboard');
    })->name('dashboard');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    Route::get('/application', function () {
        return view('applicant.application');
    })->name('application');

    Route::get('/files', function () {
        return view('applicant.files');
    })->name('files');

    Route::get('/settings', function () {
        return view('applicant.settings');
    })->name('settings');
});

// ✅ Employee-related routes with auth middleware
Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('dashboard');

    Route::get('/profile', [UserController::class, 'showEmployee'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editEmployee'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateEmployee'])->name('profile.update');

    Route::get('/application', function () {
        return view('employee.application');
    })->name('application');

    Route::get('/files', function () {
        return view('employee.files');
    })->name('files');

    Route::get('/leaveForm', function () {
        return view('employee.leaveForm');
    })->name('leaveForm');

    Route::get('/settings', function () {
        return view('employee.settings');
    })->name('settings');
});

// Admin dashboard (auth protected)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');

// General home route (auth protected)
Route::get('/home', function () {
    return view('home'); // Ensure this view exists
})->name('home')->middleware('auth');

// Password reset placeholder
Route::get('/forgot-password', function () {
    return 'Password reset page coming soon...';
})->name('password.request');

// Fallback route for undefined pages
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
