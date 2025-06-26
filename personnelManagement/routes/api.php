<?php
use App\Models\Job;
use Illuminate\Support\Facades\Route;

Route::get('/job/{id}/applicants', function ($id) {
    $job = Job::with(['applications.user', 'applications.job'])->findOrFail($id);
    return response()->json($job->applications);
});
