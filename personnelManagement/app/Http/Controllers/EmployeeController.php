<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Employee list view
    public function index()
    {
        $role = auth()->user()->role; 

        $jobs = Job::all();

        // Fetch only employees with job_id assigned, eager load the job
        $groupedEmployees = User::where('role', 'employee')
            ->whereNotNull('job_id')
            ->with('job')
            ->get()
            ->groupBy('job_id');

        return view("$role.employees", [
            'jobs' => $jobs,
            'groupedEmployees' => $groupedEmployees,
        ]);
    }

    // Performance Evaluation view for hrStaff
   public function performanceEvaluation()
{
    $jobs = Job::all();

    // Applicants ready for evaluation (with training scheduled)
    $applicants = Application::with(['user', 'trainingSchedule'])
        ->where('status', 'scheduled_for_training')
        ->whereHas('trainingSchedule', function ($query) {
            $query->where('status', 'scheduled');
        })
        ->get();

    // Employees already hired
    $employees = User::where('role', 'employee')
        ->with('job')
        ->get();

    return view('hrStaff.perfEval', [
        'jobs' => $jobs,
        'applicants' => $applicants,
        'employees' => $employees,
    ]);
}

}
