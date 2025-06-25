<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job; // ✅ Add this line
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->get();
        $jobs = Job::all(); // ✅ Get all job postings

        return view('hrAdmin.employees', [
            'employees' => $employees,
            'jobs' => $jobs,
        ]);
    }
}
