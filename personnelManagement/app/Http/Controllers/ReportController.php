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
    // Debug endpoint to see what parameters are being received
    public function debugApplicants(Request $request)
    {
        return response()->json([
            'status' => $request->get('status'),
            'range' => $request->get('range'),
            'company' => $request->get('company'),
            'job_id' => $request->get('job_id'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'all_params' => $request->all(),
        ]);
    }

    public function applicants($format, Request $request)
    {
        // Get filter parameters
        $status = $request->get('status', 'all');
        $range = $request->get('range', 'monthly');
        $jobId = $request->get('job_id');
        $filterCompany = $request->get('company');
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        // Load full list of companies for dropdown
        $companies = DB::table('jobs')
            ->distinct()
            ->pluck('company_name');

        // -------------------------------------------------------
        // Build Query with Filters
        // -------------------------------------------------------
        $query = Application::with(['user', 'job']);

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by specific job
        if ($jobId) {
            $query->where('job_id', $jobId);
        }

        // Filter by company (only show applicants from selected company)
        if (!empty($filterCompany) && $filterCompany !== 'all') {
            $query->whereHas('job', function($q) use ($filterCompany) {
                $q->where('company_name', $filterCompany);
            });
        }

        // Filter by date range
        if ($range === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($range === 'quarterly') {
            $startOfQuarter = now()->firstOfQuarter();
            $endOfQuarter = now()->lastOfQuarter();
            $query->whereBetween('created_at', [$startOfQuarter, $endOfQuarter]);
        } elseif ($range === 'yearly') {
            $query->whereYear('created_at', now()->year);
        } elseif ($range === 'custom') {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Execute query
        $applications = $query->get();

        // -------------------------------------------------------
        // PDF Output
        // -------------------------------------------------------
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.applicants', [
                'applications' => $applications,
                'status' => $status,
                'range' => $range,
                'companies' => $companies,
                'filterCompany' => $filterCompany ?? 'all',
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->setPaper('a4', 'portrait');

            $filename = 'Applicants_'
                        . ucfirst($status) . '_'
                        . ($filterCompany && $filterCompany !== 'all' ? str_replace(' ', '_', $filterCompany) : 'All')
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
        $pdf = Pdf::loadView('reports.employee', [
            'employees' => $applications,
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