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
        $role = auth()->user()->role; // 'hrAdmin' or 'hrStaff'

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

        // Applicants ready for evaluation (with training schedule)
        $applicants = Application::with(['user', 'trainingSchedule'])
            ->where('status', 'interview_passed') // or 'training_done' depending on your logic
            ->whereHas('trainingSchedule')
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
