<?php
use App\Models\Job;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/job/{id}/applicants', function ($id) {
        // Authorization: Only HR Admin can access applicant data
        if (!auth()->user() || auth()->user()->role !== 'hradmin') {
            return response()->json([
                'message' => 'Unauthorized. Only HR administrators can access applicant data.'
            ], 403);
        }

        $job = Job::with(['applications.user', 'applications.job'])->findOrFail($id);
        return response()->json($job->applications);
    });
});
