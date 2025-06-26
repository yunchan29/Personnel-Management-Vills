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
        $employees = User::where('role', 'employee')->get();
        $jobs = Job::all();

        $role = auth()->user()->role; // 'hrAdmin' or 'hrStaff'

        return view("$role.employees", [
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }

    // Performance Evaluation view for hrStaff
    public function performanceEvaluation()
    {
        $employees = User::where('role', 'employee')->get();
        $jobs = Job::all();

        return view('hrStaff.perfEval', [
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }
}
