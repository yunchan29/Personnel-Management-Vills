<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Enums\ApplicationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    /**
     * Generate Applicant Report with proper filtering
     *
     * @param string $format - Output format (pdf)
     * @param Request $request - HTTP Request with filter parameters
     * @return \Illuminate\Http\Response
     */
    public function generateApplicantReport($format, Request $request)
    {
        // Get filter parameters with defaults
        $status = $request->get('status', 'all');
        $range = $request->get('range', 'monthly');
        $jobId = $request->get('job_id');
        $filterCompany = $request->get('company', 'all');
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        // Get list of all companies for display
        $companies = DB::table('jobs')
            ->distinct()
            ->pluck('company_name');

        // Build base query with relationships
        $query = Application::with(['user', 'job']);

        // Apply status filter
        if ($status !== 'all') {
            $statusEnum = ApplicationStatus::fromString($status);
            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        // Apply specific job filter
        if ($jobId) {
            $query->where('job_id', $jobId);
        }

        // Apply company filter using relationship
        if ($filterCompany !== 'all' && !empty($filterCompany)) {
            $query->whereHas('job', function($q) use ($filterCompany) {
                $q->where('company_name', $filterCompany);
            });
        }

        // Apply date range filters based on created_at
        $query = $this->applyDateRangeFilter($query, $range, $startDate, $endDate);

        // Execute query and get results
        $applications = $query->orderBy('created_at', 'desc')->get();

        // Handle PDF generation
        if ($format === 'pdf') {
            return $this->generateApplicantPDF($applications, [
                'status' => $status,
                'range' => $range,
                'filterCompany' => $filterCompany,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'companies' => $companies,
            ]);
        }

        abort(404, 'Unsupported format');
    }

    /**
     * Generate Employee Report with proper filtering
     *
     * @param string $format - Output format (pdf)
     * @param Request $request - HTTP Request with filter parameters
     * @return \Illuminate\Http\Response
     */
    public function generateEmployeeReport($format, Request $request)
    {
        // Extract filter parameters
        $range = $request->get('range', 'all');
        $filterCompany = $request->get('company', 'all');
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        // DEBUG: Log received parameters
        \Log::info('Employee Report Parameters:', [
            'range' => $range,
            'filterCompany' => $filterCompany,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'all_params' => $request->all()
        ]);

        // Get list of companies from jobs table
        $companies = DB::table('jobs')
            ->distinct()
            ->pluck('company_name');

        // Build base query for employees
        // Get applications that have contract start dates (these are hired employees)
        $query = Application::with(['user', 'job'])
            ->whereNotNull('contract_start'); // Must have contract start date

        // DEBUG: Log query before filters
        \Log::info('Total employees before filters:', ['count' => $query->count()]);

        // Apply company filter
        if ($filterCompany !== 'all' && !empty($filterCompany)) {
            $query->whereHas('job', function($q) use ($filterCompany) {
                $q->where('company_name', $filterCompany);
            });
            // DEBUG: Log query after company filter
            \Log::info('Employees after company filter:', ['company' => $filterCompany, 'count' => $query->count()]);
        }

        // Apply date range filters based on contract_start
        $query = $this->applyEmployeeDateRangeFilter($query, $range, $startDate, $endDate);

        // DEBUG: Log query after date filter
        \Log::info('Employees after date filter:', ['range' => $range, 'count' => $query->count()]);

        // Execute query
        $employees = $query->orderBy('contract_start', 'desc')->get();

        // Handle PDF generation
        if ($format === 'pdf') {
            return $this->generateEmployeePDF($employees, [
                'range' => $range,
                'filterCompany' => $filterCompany,
                'start' => $startDate,
                'end' => $endDate,
                'companies' => $companies,
            ]);
        }

        abort(404, 'Unsupported format');
    }

    /**
     * Apply date range filter to applicant query (based on created_at)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $range
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDateRangeFilter($query, $range, $startDate = null, $endDate = null)
    {
        switch ($range) {
            case 'monthly':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;

            case 'quarterly':
                $startOfQuarter = Carbon::now()->firstOfQuarter();
                $endOfQuarter = Carbon::now()->lastOfQuarter();
                $query->whereBetween('created_at', [$startOfQuarter, $endOfQuarter]);
                break;

            case 'yearly':
                $query->whereYear('created_at', Carbon::now()->year);
                break;

            case 'custom':
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                break;
        }

        return $query;
    }

    /**
     * Apply date range filter to employee query (based on contract_start)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $range
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyEmployeeDateRangeFilter($query, $range, $startDate = null, $endDate = null)
    {
        switch ($range) {
            case 'all':
                // No date filtering - show all employees regardless of hire date
                break;

            case 'monthly':
                $query->whereMonth('contract_start', Carbon::now()->month)
                      ->whereYear('contract_start', Carbon::now()->year);
                break;

            case 'quarterly':
                $startOfQuarter = Carbon::now()->firstOfQuarter();
                $endOfQuarter = Carbon::now()->lastOfQuarter();
                $query->whereBetween('contract_start', [$startOfQuarter, $endOfQuarter]);
                break;

            case 'yearly':
                $query->whereYear('contract_start', Carbon::now()->year);
                break;

            case 'custom':
                if ($startDate) {
                    $query->whereDate('contract_start', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('contract_start', '<=', $endDate);
                }
                break;
        }

        return $query;
    }

    /**
     * Generate PDF for applicant report
     *
     * @param \Illuminate\Database\Eloquent\Collection $applications
     * @param array $metadata
     * @return \Illuminate\Http\Response
     */
    private function generateApplicantPDF($applications, $metadata)
    {
        $pdf = Pdf::loadView('reports.applicants', array_merge([
            'applications' => $applications,
        ], $metadata))
        ->setPaper('a4', 'portrait');

        // Generate filename
        $filename = sprintf(
            'Applicants_%s_%s_%s.pdf',
            ucfirst($metadata['status']),
            $metadata['filterCompany'] !== 'all' ? str_replace(' ', '_', $metadata['filterCompany']) : 'All',
            Carbon::now()->format('M-d-Y')
        );

        return $pdf->download($filename);
    }

    /**
     * Generate PDF for employee report
     *
     * @param \Illuminate\Database\Eloquent\Collection $employees
     * @param array $metadata
     * @return \Illuminate\Http\Response
     */
    private function generateEmployeePDF($employees, $metadata)
    {
        $pdf = Pdf::loadView('reports.employee', array_merge([
            'employees' => $employees,
        ], $metadata))
        ->setPaper('a4', 'portrait');

        // Generate filename
        $filename = sprintf(
            'Employees_%s_%s_%s.pdf',
            ucfirst($metadata['range']),
            $metadata['filterCompany'] !== 'all' ? str_replace(' ', '_', $metadata['filterCompany']) : 'All',
            Carbon::now()->format('M-d-Y')
        );

        return $pdf->download($filename);
    }
}
