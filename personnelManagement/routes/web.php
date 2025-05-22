<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\JobController; 
use App\Http\Controllers\ApplicantJobController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\File201Controller;
use App\Http\Controllers\ResumeController;

Route::get('/jobs/{id}', [JobController::class, 'show'])->name('job.show');

Route::get('/', [LandingPageController::class, 'index']);


/*Route::post('/leave/store', [LeaveController::class, 'store'])->name('leave.store');

Route::middleware(['auth'])->group(function () {
    Route::post('/leave/store', [LeaveController::class, 'store'])->name('leave.store');
});*/


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


// Layout previews (optional)
Route::get('/layouts/applicantHome', function () {
    return view('layouts.applicantHome');
})->name('applicantHome');

Route::get('/layouts/employeeHome', function () {
    return view('layouts.employeeHome');
})->name('employeeHome');

Route::get('/', [LandingPageController::class, 'index'])->name('welcome');


// ✅ Applicant-related routes with auth middleware
Route::prefix('applicant')->name('applicant.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('applicant.dashboard');
    })->name('dashboard');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    Route::get('/files', function () {
        return view('applicant.files');

    })->name('files');

    Route::get('/settings', function () {
        return view('applicant.settings');
    })->name('settings');

         Route::get('/application', [ResumeController::class, 'show'])
              ->name('application');

         // POST   → applicant.application.store
         Route::post('/application', [ResumeController::class, 'store'])
              ->name('application.store');

         // DELETE → applicant.application.destroy
         Route::delete('/application', [ResumeController::class, 'destroy'])
              ->name('application.destroy');

});

// ✅ Employee-related routes with auth middleware
Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('dashboard');

    Route::get('/profile', [UserController::class, 'showEmployee'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editEmployee'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateEmployee'])->name('profile.update');

   Route::get('/application', [ResumeController::class, 'show'])
              ->name('application');

         // POST   → applicant.application.store
         Route::post('/application', [ResumeController::class, 'store'])
              ->name('application.store');

         // DELETE → applicant.application.destroy
         Route::delete('/application', [ResumeController::class, 'destroy'])
              ->name('application.destroy');

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



// ✅ HRadmin-related routes with auth middleware
Route::prefix('hrAdmin')->name('hrAdmin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('hrAdmin.dashboard');
    })->name('dashboard');

    Route::get('/profile', [UserController::class, 'showHrAdmin'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editHrAdmin'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateHrAdmin'])->name('profile.update');

    Route::get('/application', function () {
        return view('hrAdmin.application');
    })->name('application');

    Route::get('/jobPosting', function () {
        return view('hrAdmin.jobPosting');
    })->name('jobPosting');

    // ✅ Job posting form submit route
    Route::post('/jobPosting/store', [JobController::class, 'store'])->name('jobPosting.store');

    Route::get('/files', function () {
        return view('hrAdmin.files');
    })->name('files');

    Route::get('/leaveForm', function () {
        return view('hrAdmin.leaveForm');
    })->name('leaveForm');

    Route::get('/settings', function () {
        return view('hrAdmin.settings');
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

Route::middleware(['auth'])->group(function () {
    Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('user.changePassword');
    Route::delete('/user/delete-account', [UserController::class, 'deleteAccount'])->name('user.deleteAccount');
});

Route::get('/hr-admin/job-posting', [JobController::class, 'index'])->name('hrAdmin.jobPosting');
Route::post('/hr-admin/job-posting', [JobController::class, 'store'])->name('hrAdmin.jobPosting.store');

Route::get('/applicant/dashboard', [ApplicantJobController::class, 'dashboard'])->name('applicant.dashboard');



Route::post('/file201', [File201Controller::class, 'store'])->name('file201.store');

Route::get('/file201/form', [File201Controller::class, 'form'])->name('file201.form');


