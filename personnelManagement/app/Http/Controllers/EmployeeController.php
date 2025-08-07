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

        // Fetch employees with job assignments
        $employees = User::where('role', 'employee')
            ->whereNotNull('job_id')
            ->with('job')
            ->get();

        // Group employees by job_id
        $groupedEmployees = $employees->groupBy('job_id');

        return view("$role.employees", [
            'jobs' => $jobs,
            'employees' => $employees, // ✅ Add this so $employees is defined
            'groupedEmployees' => $groupedEmployees,
        ]);
    }

    public function performanceEvaluation()
    {
        $jobs = Job::all();

        // Fetch all applicants who have a training schedule
        $applicants = Application::with(['user', 'trainingSchedule', 'evaluation'])
            ->whereHas('trainingSchedule')
            ->get();

        // Fetch employees if needed
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
