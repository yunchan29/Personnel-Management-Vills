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

public function performanceEvaluation(Request $request)
{
    $viewType = $request->query('view', 'pending'); // default = pending

    if ($viewType === 'all') {
        // Jobs (all with applications that are not archived)
        $jobs = Job::whereHas('applications', function ($query) {
                $query->whereHas('trainingSchedule')
                      ->where('is_archived', false);
            })
            ->with(['applications' => function ($query) {
                $query->whereHas('trainingSchedule')
                      ->where('is_archived', false)
                      ->with(['user', 'evaluation']);
            }])
            ->get();

        // Applicants (all not archived)
        $applicants = Application::with(['user', 'job', 'evaluation'])
            ->whereHas('trainingSchedule')
            ->where('is_archived', false)
            ->get();

        // Employees (all)
        $employees = User::where('role', 'employee')
            ->with('job')
            ->get();

    } else {
        // === PENDING (default) ===
        $jobs = Job::whereHas('applications', function($query) {
                $query->whereHas('trainingSchedule')
                      ->where('is_archived', false)
                      ->where(function ($q) {
                          $q->whereDoesntHave('evaluation') // pending
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

        $employees = User::where('role', 'employee')
            ->with('job')
            ->get();
    }

    return view('hrStaff.perfEval', [
        'jobs' => $jobs,
        'applicants' => $applicants,
        'employees' => $employees,
        'viewType' => $viewType, // pass to blade so we know which toggle is active
    ]);
}


}
