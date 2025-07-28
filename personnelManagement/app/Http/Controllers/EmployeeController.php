<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
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
        $employees = User::where('role', 'employee')
            ->with('job') // optional but useful if needed in view
            ->get();

        $jobs = Job::all();

        return view('hrStaff.perfEval', [
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }
}
