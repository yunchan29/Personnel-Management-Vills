<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class LandingPageController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');

    $jobs = Job::query()
        ->notExpired()
        ->when($search, function ($query, $search) {
            $query->where('job_title', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(4)
        ->appends(['search' => $search]); // Keeps search term during pagination

    return view('welcome', compact('jobs', 'search'));
}

}
