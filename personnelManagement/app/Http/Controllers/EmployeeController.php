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
    // Jobs with applicants (pending or passed, not failed, not archived)
    $jobs = Job::whereHas('applications', function($query) {
        $query->whereHas('trainingSchedule')
              ->where('is_archived', false)
              ->where(function ($q) {
                  $q->whereDoesntHave('evaluation') // not yet evaluated
                    ->orWhereHas('evaluation', function ($sub) {
                        $sub->where('result', 'passed'); // keep passed
                    });
              });
    })
    ->with(['applications' => function($query) {
        $query->whereHas('trainingSchedule')
              ->where('is_archived', false)
              ->where(function ($q) {
                  $q->whereDoesntHave('evaluation')
                    ->orWhereHas('evaluation', function ($sub) {
                        $sub->where('result', 'passed');
                    });
              })
              ->with(['user', 'evaluation']);
    }])
    ->get();

    // Applicants (pending or passed, not archived)
    $applicants = Application::with(['user', 'job', 'evaluation'])
        ->whereHas('trainingSchedule')
        ->where('is_archived', false)
        ->where(function ($q) {
            $q->whereDoesntHave('evaluation')
              ->orWhereHas('evaluation', function ($sub) {
                  $sub->where('result', 'passed');
              });
        })
        ->get();

    // Employees
    $employees = User::where('role', 'employee')
        ->with('job')
        ->get();

    return view('hrStaff.perfEval', compact('jobs', 'applicants', 'employees'));
}


}
