<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Http\Controllers\JobController; 
use App\Http\Controllers\ApplicantJobController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\File201Controller;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\LeaveFormController;
use App\Http\Controllers\EmployeeController;


//temporary ulit HAHAHAHH sorry
Route::get('/job/{id}', [JobController::class, 'show'])->name('job.show');

// Landing page route
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);


// Temporary route for testing
Route::get('/job/{id}', [JobController::class, 'show'])->name('job.show');


// ✅ Applicant-related routes with auth middleware
Route::prefix('applicant')->name('applicant.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('dashboard', [ApplicantJobController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings', fn () => view('applicant.settings'))->name('settings');
    Route::post('/user/toggle-visibility', [UserController::class, 'toggleVisibility'])->name('user.toggleVisibility');


    // Resume and Applications
    Route::get('/application', [ResumeController::class, 'show'])->name('application');
    Route::post('/application', [ResumeController::class, 'store'])->name('application.store');
    Route::delete('/application', [ResumeController::class, 'destroy'])->name('application.destroy');
    Route::delete('/application/{id}/delete', [ResumeController::class, 'deleteApplication'])->name('application.delete');

    // Government IDs and Licenses (File 201)
    Route::post('/files', [File201Controller::class, 'store'])->name('files.store');
    Route::get('/files', [File201Controller::class, 'show'])->name('files');

    // Applications listing
    Route::get('/my-applications', [ApplicantJobController::class, 'myApplications'])->name('applicant.applications');

});


// ✅ Employee-related routes with auth middleware
Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {return view('employee.dashboard');})->name('dashboard');

    // Profile routes to edit and update user profile
    Route::get('/profile', [UserController::class, 'showEmployee'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editEmployee'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateEmployee'])->name('profile.update');

    // Resume routes (Upload and delete)
    Route::get('/application', [ResumeController::class, 'show'])->name('application');
    Route::post('/application', [ResumeController::class, 'store'])->name('application.store');
    Route::delete('/application', [ResumeController::class, 'destroy'])->name('application.destroy');

    // Change password route
    Route::get('/settings', function () {return view('employee.settings');})->name('settings');

    // ✅ Leave Form Routes
    Route::get('/leave-forms', [LeaveFormController::class, 'index'])->name('leaveForm'); 
    Route::post('/leave-forms', [LeaveFormController::class, 'store'])->name('leaveForms.store');
    Route::delete('/leave-forms/{id}', [LeaveFormController::class, 'destroy'])->name('leaveForms.destroy');

    // Government IDs and Licenses (File 201) routes
    Route::post('/files', [File201Controller::class, 'store'])->name('files.store');
    Route::get('/files', [File201Controller::class, 'show'])->name('files');
    Route::delete('/files/{id}', [File201Controller::class, 'destroy'])->name('files.destroy');

});


// ✅ HRadmin-related routes with auth middleware
Route::prefix('hrAdmin')->name('hrAdmin.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('hrAdmin.dashboard'))->name('dashboard');

    // Profile Management
    Route::get('/profile', [UserController::class, 'showHrAdmin'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editHrAdmin'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateHrAdmin'])->name('profile.update');

    // Application Viewing
    Route::get('/application', [JobController::class, 'applications'])->name('application');
    Route::get('/viewApplication', [JobController::class, 'viewApplications'])->name('viewApplication');
    Route::get('/viewApplicants/{id}', [JobController::class, 'viewApplicants'])->name('viewApplicants');
    Route::get('/job/{id}/applicants', [JobController::class, 'viewApplicants'])->name('applicants');

    // Applicant Actions
Route::post('/applications/{id}/status', [JobController::class, 'updateApplicationStatus'])->name('applications.updateStatus');
Route::post('/applications/{id}/interview-date', [JobController::class, 'setInterviewDate'])->name('applications.setInterviewDate');
Route::post('/applications/{id}/training-date', [JobController::class, 'setTrainingDate'])->name('applications.setTrainingDate'); // ✅ Add this

    // Job Posting CRUD
    Route::get('/job-posting', [JobController::class, 'index'])->name('jobPosting');
    Route::post('/jobPosting/store', [JobController::class, 'store'])->name('jobPosting.store');
    Route::get('/job-posting/{id}/edit', [JobController::class, 'edit'])->name('jobPosting.edit');
    Route::put('/jobPosting/{id}', [JobController::class, 'update'])->name('jobPosting.update');
    Route::get('/job-posting/{id}', [JobController::class, 'show'])->name('jobPosting.show');
    Route::delete('/jobPosting/{id}', [JobController::class, 'destroy'])->name('jobPosting.destroy');

    // 201 Files: Government IDs and Licenses
    Route::get('/files', fn() => view('hrAdmin.files'))->name('files');

    // Leave Form
    Route::get('/leave-forms', [LeaveFormController::class, 'index'])->name('leaveForm');
    Route::post('/leave-forms', [LeaveFormController::class, 'store'])->name('leaveForms.store');
    Route::delete('/leave-forms/{id}', [LeaveFormController::class, 'destroy'])->name('leaveForms.destroy');
    Route::post('/leave-forms/{id}/approve', [LeaveFormController::class, 'approve'])->name('leaveForms.approve');
    Route::post('/leave-forms/{id}/decline', [LeaveFormController::class, 'decline'])->name('leaveForms.decline');

    // Employee Listing
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');

    // Settings
    Route::get('/settings', fn() => view('hrAdmin.settings'))->name('settings');

    // Training Schedule
    Route::get('/training-schedule', [JobController::class, 'trainingSchedule'])->name('training.schedule');
});

    
// Fallback route for undefined pages
Route::fallback(function () {return response()->view('errors.404', [], 404);});

// Route parin ng change password at delete account
Route::middleware(['auth'])->group(function () {
    Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('user.changePassword');
    Route::delete('/user/delete-account', [UserController::class, 'deleteAccount'])->name('user.deleteAccount');
});

// Di ko alam kung anong gagawin dito, pero baka kailangan mo ng mga routes para sa mga admin at home page
// Admin dashboard (auth protected)
Route::get('/admin/dashboard', function () {return view('admin.dashboard');})->name('admin.dashboard')->middleware('auth');

// General home route (auth protected)
Route::get('/home', function () {return view('home'); // Ensure this view exists
})->name('home')->middleware('auth');

// Password reset 
Route::get('/forgot-password', function () {
    return view('auth.forgot-password'); // Change this to load the Blade
})->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password', function (Request $request) {
    return view('auth.reset-password', [
        'token' => $request->query('token'),
        'email' => $request->query('email')
    ]);
})->name('password.reset');

Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update');

// Apply route for applicants (di ko pa na sosort nag eerror pa eh)
Route::post('/apply/{job}', [ApplicantJobController::class, 'apply'])->name('jobs.apply');


//hello
//hi

// ✅ HRstaff-related routes with auth middleware
Route::prefix('hrStaff')->name('hrStaff.')->middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('hrStaff.dashboard');
    })->name('dashboard');


Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');

Route::get('/perfEval', [EmployeeController::class, 'performanceEvaluation'])->name('perfEval');

      // Leave Form routes (Pre-made)
    Route::get('/leave-forms', [LeaveFormController::class, 'index'])->name('leaveForm'); 
    Route::post('/leave-forms', [LeaveFormController::class, 'store'])->name('leaveForms.store');
    Route::delete('/leave-forms/{id}', [LeaveFormController::class, 'destroy'])->name('leaveForms.destroy');


Route::post('/leave-forms/{id}/approve', [LeaveFormController::class, 'approve'])->name('leaveForms.approve');
Route::post('/leave-forms/{id}/decline', [LeaveFormController::class, 'decline'])->name('leaveForms.decline');

    // Change password/settings route
    Route::get('/settings', function () {
        return view('hrStaff.settings');
    })->name('settings');

});







