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
    if (!empty($filterCompany) && $filterCompany !== 'all') {
        $query->whereHas('job', function ($q) use ($filterCompany) {
            $q->where('company_name', $filterCompany);
        });
    }

    $applications = $query->get();

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
}