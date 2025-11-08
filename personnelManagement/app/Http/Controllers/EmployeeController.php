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

        return view('admins.shared.employees', [
            'jobs' => $jobs,
            'employees' => $employees,
            'groupedEmployees' => $groupedEmployees,
        ]);
    }
    // Performance Evaluation view  
public function performanceEvaluation(Request $request)
{
    // Jobs: must have at least one qualifying application (not archived, training schedule)
    $jobs = Job::whereHas('applications', function ($query) {
            $query->whereHas('trainingSchedule')
                  ->where('is_archived', false)
                  ->where(function ($q) {
                      $q->whereDoesntHave('evaluation') // no evaluation yet
                        ->orWhereHas('evaluation', function ($sub) {
                            $sub->where('result', 'passed'); // passed evaluation
                        });
                  });
        })
        
        ->withCount(['applications as applications_count' => function ($query) {
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

    // Applicants: must have training schedule, not archived, and meet same eval conditions
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

    // Employees: all employees
    $employees = User::where('role', 'employee')
        ->with('job')
        ->get();

    return view('admins.hrStaff.perfEval', [
        'jobs'       => $jobs,
        'applicants' => $applicants,
        'employees'  => $employees,
    ]);
}



}
