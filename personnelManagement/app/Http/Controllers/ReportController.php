<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function applicants($format, Request $request)
{
    $status = $request->get('status', 'all');
    $range = $request->get('range', 'monthly');
    $jobId = $request->get('job_id');
    $filterCompany = $request->get('company'); // do not force "all"

    // -------------------------------------------------------
    // ✅ ALWAYS load full list of companies (for dropdown)
    // -------------------------------------------------------
    $companies = DB::table('jobs')
        ->distinct()
        ->pluck('company_name');

    // -------------------------------------------------------
    // ✅ Query applicants
    // -------------------------------------------------------
    $query = Application::with(['user', 'job']);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($jobId) {
        $query->where('job_id', $jobId);
    }

    // -------------------------------------------------------
    // ✅ STRICT company filter (only show applicants from selected company)
    // -------------------------------------------------------
    // Filter by company (based on job table)
if (!empty($filterCompany) && $filterCompany !== 'all') {

    // Get all job IDs belonging to the selected company
    $jobIds = Job::where('company_name', $filterCompany)->pluck('id');

    // Only return applicants from those jobs
    $query->whereIn('job_id', $jobIds);
}
$applications = $query->get();  // <-- THIS IS MANDATORY

    // -------------------------------------------------------
    // ✅ PDF Output
    // -------------------------------------------------------
    if ($format === 'pdf') {
        $pdf = Pdf::loadView('reports.applicants', [
            'applications' => $applications,
            'status' => $status,
            'range' => $range,
            'companies' => $companies,
            'filterCompany' => $filterCompany,
        ])->setPaper('a4', 'portrait');

        $filename = 'Applicants_'
                    . ucfirst($status) . '_'
                    . ($filterCompany ? str_replace(' ', '_', $filterCompany) : 'All')
                    . '_' . now()->format('M-d-Y') . '.pdf';

        return $pdf->download($filename);
    }

    abort(404, 'Unsupported format');
}

public function employees($format, Request $request)
{
    $range = $request->get('range', 'monthly');
    $filterCompany = $request->get('company', 'all');

    $start = $request->get('start');
    $end   = $request->get('end');

    // -------------------------------------------------------
    // ✅ Get list of companies from applications table
    // -------------------------------------------------------
    $companies = Application::distinct()
        ->join('jobs', 'applications.job_id', '=', 'jobs.id')
        ->pluck('jobs.company_name');

    // -------------------------------------------------------
    // ✅ Base Query — employees only (roles = employee)
    // -------------------------------------------------------
    $query = Application::with(['user', 'job'])
        ->whereNotNull('start_date') // employee must have start date
        ->whereHas('user', function ($q) {
            $q->where('role', 'employee');
        });

    // -------------------------------------------------------
    // ✅ Company Filter
    // -------------------------------------------------------
    if ($filterCompany !== 'all') {
        $query->whereHas('job', function ($q) use ($filterCompany) {
            $q->where('company_name', $filterCompany);
        });
    }

    // -------------------------------------------------------
    // ✅ Date Range Filters (based on start_date)
    // -------------------------------------------------------
    if ($range === 'monthly') {
        $query->whereMonth('contract_start', now()->month)
              ->whereYear('contract_start', now()->year);
    } 
    else if ($range === 'weekly') {
        $query->whereBetween('contract_start', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    } 
    else if ($range === 'yearly') {
        $query->whereYear('contract_start', now()->year);
    }

    $applications = $query->get();

    // -------------------------------------------------------
    // ✅ PDF Output
    // -------------------------------------------------------
    if ($format === 'pdf') {
        $pdf = Pdf::loadView('reports.employees', [
            'employees' => $employees,
            'range' => $range,
            'filterCompany' => $filterCompany,
            'start' => $start,
            'end' => $end,
            'companies' => $companies,
        ])->setPaper('a4', 'portrait');

        $filename = 'Employees_' .
            ucfirst($range) . '_' .
            ($filterCompany ? str_replace(' ', '_', $filterCompany) : 'All') .
            '_' . now()->format('M-d-Y') .
            '.pdf';

        return $pdf->download($filename);
    }

    abort(404, 'Unsupported format');
}


}