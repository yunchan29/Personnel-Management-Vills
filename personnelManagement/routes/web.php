<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;



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
