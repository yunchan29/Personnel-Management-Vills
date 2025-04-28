<?php

use Illuminate\Support\Facades\Route;

// Route for homepage (welcome page)
Route::get('/', function () {
    return view('welcome');
});

// Route for login page
Route::get('/login', function () {
    return view('login');
})->name('login');

// Route for login page
Route::get('/register', function () {
    return view('register');
})->name('register');
