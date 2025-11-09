<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Employee list view
    public function index()
    {
        $role = auth()->user()->role;

        $jobs = Job::withCount('applications')->get();

        // Fetch employees with job assignments and their latest application
        $employees = User::where('role', 'employee')
            ->whereNotNull('job_id')
            ->with(['job', 'applications' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        // Add the latest application as a single object for easier access
        $employees->each(function($employee) {
            $employee->application = $employee->applications->first();
        });

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
    // Get evaluation statuses from enum
    $evaluationStatuses = ApplicationStatus::evaluationStatuses();
    $evaluationStatusValues = array_map(fn($status) => $status->value, $evaluationStatuses);

    // Jobs: must have at least one qualifying application (not archived, training schedule, correct status)
    $jobs = Job::whereHas('applications', function ($query) use ($evaluationStatusValues) {
            $query->whereHas('trainingSchedule')
                  ->where('is_archived', false)
                  ->whereIn('status', $evaluationStatusValues)
                  ->where(function ($q) {
                      $q->whereDoesntHave('evaluation') // no evaluation yet
                        ->orWhereHas('evaluation', function ($sub) {
                            $sub->where('result', 'passed'); // passed evaluation
                        });
                  });
        })

        ->withCount(['applications as applications_count' => function ($query) use ($evaluationStatusValues) {
            $query->whereHas('trainingSchedule')
                  ->where('is_archived', false)
                  ->whereIn('status', $evaluationStatusValues)
                  ->where(function ($q) {
                      $q->whereDoesntHave('evaluation')
                        ->orWhereHas('evaluation', function ($sub) {
                            $sub->where('result', 'passed');
                        });
                  })
                  ->with(['user', 'evaluation']);
        }])
        ->get();

    // Applicants: must have training schedule, not archived, correct status, and meet same eval conditions
    $applicants = Application::with(['user', 'job', 'evaluation', 'trainingSchedule'])
        ->whereHas('trainingSchedule')
        ->where('is_archived', false)
        ->whereIn('status', $evaluationStatusValues)
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
