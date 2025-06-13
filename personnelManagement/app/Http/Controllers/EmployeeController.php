<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
       $employees = User::where('role', 'employee')->get();

return view('hrAdmin.employees', ['employees' => $employees]);

    }
}
