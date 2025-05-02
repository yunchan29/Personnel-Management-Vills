<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Route for homepage (welcome page)
Route::get('/', function () {
    return view('welcome');
});

// Route for login page
Route::get('/login', function () {
    return view('login');
})->name('login');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Route for layout preview (optional)
Route::get('/layouts/applicantHome', function () {
    return view('layouts.applicantHome');
})->name('applicantHome');

// ✅ Applicant-related routes with names for sidebar matching
Route::prefix('applicant')->name('applicant.')->group(function () {
    Route::get('/dashboard', function () {
        return view('applicant.dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('applicant.profile');
    })->name('profile');

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


Route::get('/forgot-password', function () {
    return 'Password reset page coming soon...';
})->name('password.request');


// Admin dashboard route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');

// Applicant dashboard route
Route::get('/applicant/dashboard', function () {
    return view('applicant.dashboard');
})->name('applicant.dashboard')->middleware('auth');

// Home route (for fallback)
Route::get('/home', function () {
    return view('home'); // Ensure this view exists or redirect elsewhere
})->name('home')->middleware('auth');
