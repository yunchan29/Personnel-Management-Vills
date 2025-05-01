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

// Route to show registration form
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
    ->name('register');

// Route to handle registration submission
Route::post('/register', [RegisterController::class, 'register']);


// Route for layout 
Route::get('/layouts/applicantHome', function () {
    return view('layouts.applicantHome');
})->name('applicantHome');


Route::get('applicant/dashboard', function () {
    return view('applicant.dashboard');
});

