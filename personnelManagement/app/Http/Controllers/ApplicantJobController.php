<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class ApplicantJobController extends Controller
{
    // If this is for the dashboard/home view
    public function dashboard()
    {
        $jobs = Job::latest()->get(); // or ->paginate(10) if you want pagination
        return view('applicant.dashboard', compact('jobs'));
    }

    // Existing jobs page view
    public function index()
    {
        $jobs = Job::latest()->get(); // Can be reused for a separate full list view
        return view('applicant.jobs', compact('jobs'));
    }
}
