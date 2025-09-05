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

        $jobs = Job::withCount('applications')->get();

        // Fetch employees with job assignments
        $employees = User::where('role', 'employee')
            ->whereNotNull('job_id')
            ->with('job')
            ->get();

        // Group employees by job_id
        $groupedEmployees = $employees->groupBy('job_id');

        return view("$role.employees", [
            'jobs' => $jobs,
            'employees' => $employees,
            'groupedEmployees' => $groupedEmployees,
        ]);
    }

public function performanceEvaluation()
{
    // Fetch jobs that have at least one applicant pending evaluation or not yet hired
    $jobs = Job::whereHas('applications', function($query) {
        $query->where(function($q) {
            $q->whereDoesntHave('evaluation')
              ->orWhere('status', '!=', 'hired');
        });
    })->with(['applications' => function($query) {
        $query->whereHas('trainingSchedule'); // optional if you only evaluate scheduled trainings
    }])->get();

    $applicants = Application::with(['user', 'job', 'evaluation'])
        ->whereHas('trainingSchedule')
        ->get();

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
