<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobController; 
use App\Http\Controllers\ApplicantJobController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\File201Controller;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\LeaveFormController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\InitialApplicationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\DashboardChartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ArchiveController; 
use App\Http\Controllers\StaffArchiveController;
use App\Http\Controllers\ContractScheduleController; //for contract signing schedule

// Landing page route
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes Login & Logout (with rate limiting for security)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes (with rate limiting for security)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:3,1'); // 3 attempts per minute

// Email Verification routes (no auth required - user not logged in yet)
Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
Route::post('/email/verify', [VerifyEmailController::class, 'verify'])
    ->middleware('throttle:6,1')
    ->name('verification.verify');
Route::post('/email/resend', [VerifyEmailController::class, 'resend'])
    ->middleware('throttle:3,1')
    ->name('verification.resend');

// Job listing route (public)
Route::get('/job/{id}', [JobController::class, 'show'])->name('job.show');


// ✅ Applicant-related routes with auth middleware
Route::prefix('applicant')->name('applicant.')->middleware(['auth', 'verified', 'role:applicant'])->group(function () {

    // Dashboard
    Route::get('dashboard', [ApplicantJobController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/personal-info', [UserController::class, 'updatePersonalInfo'])->name('profile.updatePersonalInfo');
    Route::put('/profile/work-experience', [UserController::class, 'updateWorkExperience'])->name('profile.updateWorkExperience');
    Route::put('/profile/preference', [UserController::class, 'updatePreference'])->name('profile.updatePreference');

    // Settings
    Route::get('/settings', fn () => view('users.settings'))->name('settings');
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
Route::prefix('employee')->name('employee.')->middleware(['auth', 'verified', 'role:employee'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Get pending leave forms for the employee
        $leaveForms = \App\Models\LeaveForm::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get requirements/notifications (you can customize this based on your requirements system)
        // For now, I'll leave it as an empty collection - you can integrate with your actual requirements system
        $requirements = collect([]);

        return view('users.dashboard', compact('leaveForms', 'requirements'));
    })->name('dashboard');

    // Profile routes to edit and update user profile
    Route::get('/profile', [UserController::class, 'showEmployee'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editEmployee'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateEmployee'])->name('profile.update');
    Route::put('/profile/personal-info', [UserController::class, 'updatePersonalInfo'])->name('profile.updatePersonalInfo');
    Route::put('/profile/work-experience', [UserController::class, 'updateWorkExperience'])->name('profile.updateWorkExperience');

    // Resume routes (Upload and delete)
    Route::get('/application', [ResumeController::class, 'show'])->name('application');
    Route::post('/application', [ResumeController::class, 'store'])->name('application.store');
    Route::delete('/application', [ResumeController::class, 'destroy'])->name('application.destroy');

    // Change password route
    Route::get('/settings', function () {return view('users.settings');})->name('settings');

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
Route::prefix('hrAdmin')->name('hrAdmin.')->middleware(['auth', 'verified', 'role:hrAdmin'])->group(function () {

    // Dashboard (with chart data passed directly)
    Route::get('/dashboard', [DashboardChartController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [UserController::class, 'showHrAdmin'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editHrAdmin'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateHrAdmin'])->name('profile.update');
    Route::put('/profile/personal-info', [UserController::class, 'updatePersonalInfo'])->name('profile.updatePersonalInfo');
    Route::put('/profile/work-experience', [UserController::class, 'updateWorkExperience'])->name('profile.updateWorkExperience');

    // Application Viewing
    Route::get('/application', [InitialApplicationController::class, 'index'])->name('application');
    Route::get('/viewApplication', [InitialApplicationController::class, 'index'])->name('viewApplication');
    Route::get('/viewApplicants/{id}', [InitialApplicationController::class, 'viewApplicants'])->name('viewApplicants');
    Route::get('/job/{id}/applicants', [InitialApplicationController::class, 'viewApplicants'])->name('applicants');
    // Reports
    Route::get('/reports/applicants/{format}', [ReportController::class, 'applicants'])
    ->name('reports.applicants');


    // Interview Scheduling 
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::post('/interviews/bulk', [InterviewController::class, 'bulkStore'])->name('interviews.bulk');
    Route::post('/interviews/bulk-reschedule', [InterviewController::class, 'bulkReschedule'])->name('interviews.bulkReschedule');


    // Application Approval
    Route::post('/applications/{id}/status', [InitialApplicationController::class, 'updateApplicationStatus'])->name('applications.updateStatus');
    Route::post('/applications/bulk-status', [InitialApplicationController::class, 'bulkUpdateStatus'])->name('applications.bulkUpdateStatus');

    // Training Schedule
    Route::post('/applications/{id}/training-date', [TrainingScheduleController::class, 'setTrainingDate'])->name('applications.setTrainingDate');
    // Training Schedule
    Route::post('/training-schedule/bulk', [TrainingScheduleController::class, 'bulkSetTraining'])->name('training.schedule.bulk');
    


    // Job Posting CRUD
    Route::get('/job-posting', [JobController::class, 'index'])->name('jobPosting');
    Route::post('/jobPosting/store', [JobController::class, 'store'])->name('jobPosting.store');
    Route::get('/job-posting/{id}/edit', [JobController::class, 'edit'])->name('jobPosting.edit');
    Route::put('/jobPosting/{id}', [JobController::class, 'update'])->name('jobPosting.update');
    Route::get('/job-posting/{id}', [JobController::class, 'show'])->name('jobPosting.show');
    Route::delete('/jobPosting/{id}', [JobController::class, 'destroy'])->name('jobPosting.destroy');

    // 201 Files: Government IDs and Licenses
    Route::get('/files', fn() => view('users.files'))->name('files');

  Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
  Route::get('/archive/{id}', [ArchiveController::class, 'show'])->name('archive.show');
  Route::delete('/archive/{id}', [ArchiveController::class, 'destroy'])->name('archive.destroy');



    // Leave Form
    Route::get('/leave-forms', [LeaveFormController::class, 'index'])->name('leaveForm');
    Route::post('/leave-forms', [LeaveFormController::class, 'store'])->name('leaveForms.store');
    Route::delete('/leave-forms/{id}', [LeaveFormController::class, 'destroy'])->name('leaveForms.destroy');
    Route::post('/leave-forms/{id}/approve', [LeaveFormController::class, 'approve'])
        ->middleware('throttle:30,1') // 30 approvals per minute max
        ->name('leaveForms.approve');
    Route::post('/leave-forms/{id}/decline', [LeaveFormController::class, 'decline'])
        ->middleware('throttle:30,1') // 30 declines per minute max
        ->name('leaveForms.decline');

    // Employee Listing
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');

    // Settings
    Route::get('/settings', fn() => view('admins.shared.settings'))->name('settings');

    // Training Schedule
    Route::post('/training-schedule/{id}', [TrainingScheduleController::class, 'setTrainingDate'])
        ->middleware('throttle:20,1') // 20 schedules per minute max
        ->name('training.schedule.set');

});

    
// Fallback route for undefined pages
Route::fallback(function () {return response()->view('errors.404', [], 404);});

// Route parin ng change password at delete account
Route::middleware(['auth'])->group(function () {
    Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('user.changePassword');
    Route::delete('/user/delete-account', [UserController::class, 'deleteAccount'])->name('user.deleteAccount');

    // Employee Details API for modal (rate limited to prevent abuse)
    Route::get('/users/{id}/details', [UserController::class, 'getEmployeeDetails'])
        ->middleware('throttle:60,1') // 60 requests per minute
        ->name('users.details');

    // File 201 (Requirements) view for employees (rate limited to prevent scraping)
    Route::get('/file-201/{id}', [File201Controller::class, 'showApplicantFiles'])
        ->middleware('throttle:30,1') // 30 requests per minute
        ->name('file201.show');

    // ✅ SECURITY FIX: Secure file serving routes with authentication
    Route::get('/secure/resume/{filename}', [\App\Http\Controllers\SecureFileController::class, 'serveResume'])->name('secure.resume');
    Route::get('/secure/other-file/{filename}', [\App\Http\Controllers\SecureFileController::class, 'serveOtherFile'])->name('secure.otherFile');
    Route::get('/secure/profile-picture/{filename}', [\App\Http\Controllers\SecureFileController::class, 'serveProfilePicture'])->name('secure.profilePicture');
    Route::get('/secure/resume-snapshot/{filename}', [\App\Http\Controllers\SecureFileController::class, 'serveResumeSnapshot'])->name('secure.resumeSnapshot');
});

// Password reset (with rate limiting for security)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password'); // Change this to load the Blade
})->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:3,1') // 3 attempts per minute
    ->name('password.email');

Route::get('/reset-password', function (Request $request) {
    return view('auth.reset-password', [
        'token' => $request->query('token'),
        'email' => $request->query('email')
    ]);
})->name('password.reset');

Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])
    ->middleware('throttle:5,1') // 5 attempts per minute
    ->name('password.update');

// Apply route for applicants (di ko pa na sosort nag eerror pa eh)
Route::post('/apply/{job}', [ApplicantJobController::class, 'apply'])
    ->middleware('throttle:10,1') // 10 applications per minute max
    ->name('jobs.apply');


//hello
//hi

// ✅ HRstaff-related routes with auth middleware
Route::prefix('hrStaff')->name('hrStaff.')->middleware(['auth', 'verified', 'role:hrStaff'])->group(function () {

    // Dashboard route
    Route::get('/dashboard', function () {
        // Count applicants in Interview Schedule (with scheduled interview)
        $interviewScheduleCount = \App\Models\Application::whereIn('status', ['approved', 'for_interview', 'interviewed', 'declined'])
            ->whereHas('interview', function($query) {
                $query->whereNotNull('scheduled_at');
            })
            ->count();

        // Count applicants in Training Schedule (with scheduled training)
        $trainingScheduleCount = \App\Models\Application::whereIn('status', ['interviewed', 'scheduled_for_training'])
            ->whereHas('trainingSchedule', function($query) {
                $query->whereNotNull('start_date')
                      ->whereNotNull('end_date');
            })
            ->count();

        // Count applicants pending for evaluation (has training schedule but no evaluation)
        $pendingEvaluationCount = \App\Models\Application::where('status', 'scheduled_for_training')
            ->whereHas('trainingSchedule')
            ->whereDoesntHave('evaluation')
            ->count();

        return view('admins.hrStaff.dashboard', compact(
            'interviewScheduleCount',
            'trainingScheduleCount',
            'pendingEvaluationCount'
        ));
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [UserController::class, 'showHrStaff'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editHrStaff'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateHrStaff'])->name('profile.update');
    Route::put('/profile/personal-info', [UserController::class, 'updatePersonalInfo'])->name('profile.updatePersonalInfo');
    Route::put('/profile/work-experience', [UserController::class, 'updateWorkExperience'])->name('profile.updateWorkExperience');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');

    // Performance Evaluation Page
    Route::get('/perfEval', [EmployeeController::class, 'performanceEvaluation'])->name('perfEval');

    // ✅ HR Staff - View Applicant Requirements (for requirementsModal in perfEval)
   
    Route::get('/requirements/{applicant}', [File201Controller::class, 'showApplicantFiles'])
        ->name('requirements.show');

    // ✅ HR Staff - Send Missing Requirements Email
    Route::post('/applicants/{id}/send-missing-requirements', [File201Controller::class, 'sendMissingRequirements'])
    ->name('applicants.sendMissingRequirements');


    // Evaluations - Submit Evaluation for an Applicant
    Route::post('/evaluations/{application}', [EvaluationController::class, 'store'])
        ->name('evaluations.store');

    // Leave Form Routes
    Route::get('/leave-forms', [LeaveFormController::class, 'index'])->name('leaveForm'); 
    Route::post('/leave-forms', [LeaveFormController::class, 'store'])->name('leaveForms.store');
    Route::delete('/leave-forms/{id}', [LeaveFormController::class, 'destroy'])->name('leaveForms.destroy');
    Route::post('/leave-forms/{id}/approve', [LeaveFormController::class, 'approve'])->name('leaveForms.approve');
    Route::post('/leave-forms/{id}/decline', [LeaveFormController::class, 'decline'])->name('leaveForms.decline');

    // Manual promotion from applicant to employee
    Route::post('/evaluation/promote/{application}', [EvaluationController::class, 'promoteApplicant'])
        ->name('evaluation.promote');

    // Archive
    Route::get('/archive', [StaffArchiveController::class, 'index'])->name('archive.index');
    Route::get('/archive/{id}', [StaffArchiveController::class, 'show'])->name('archive.show');
    Route::post('/archive/{id}', [StaffArchiveController::class, 'store'])->name('archive.store');
    Route::put('/archive/{id}/restore', [StaffArchiveController::class, 'restore'])->name('archive.restore');

    // Settings / Change Password
    Route::get('/settings', function () {
        return view('admins.shared.settings');
    })->name('settings');

    // ✅ Contract Signing Schedule Routes
    Route::post('/contract-schedule/{application}', [\App\Http\Controllers\ContractScheduleController::class, 'store'])
        ->name('contractSchedule.store');
    Route::delete('/contract-schedule/{application}', [\App\Http\Controllers\ContractScheduleController::class, 'destroy'])
        ->name('contractSchedule.destroy');
        
    // ✅ Contract Dates Routes
    Route::post('/contract-dates/{application}', [\App\Http\Controllers\ContractScheduleController::class, 'storeDates'])
        ->name('contractDates.store');
    Route::delete('/contract-dates/{application}', [\App\Http\Controllers\ContractScheduleController::class, 'destroyDates'])
        ->name('contractDates.destroy');
});






