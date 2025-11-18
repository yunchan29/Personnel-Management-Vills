<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveForm;
use App\Models\Application;
use App\Models\Interview;
use App\Models\TrainingSchedule;
use App\Models\TrainingEvaluation;
use App\Models\Job;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardReportController extends Controller
{
    public function generatePDF(Request $request)
    {
        // Get filter parameters
        $filterCompany = $request->input('company');
        $filterStartDate = $request->input('start_date');
        $filterEndDate = $request->input('end_date');

        // Helper function: Apply date filters to query
        $applyDateFilter = function ($query, $filterStartDate, $filterEndDate) {
            if ($filterStartDate) {
                $query->whereDate('created_at', '>=', $filterStartDate);
            }
            if ($filterEndDate) {
                $query->whereDate('created_at', '<=', $filterEndDate);
            }
            return $query;
        };

        // Get distinct company names
        $companiesQuery = DB::table('jobs')->distinct();
        if ($filterCompany) {
            $companiesQuery->where('company_name', $filterCompany);
        }
        $companies = $companiesQuery->pluck('company_name');

        // ==============================
        // Totals for Stat Cards
        // ==============================
        $jobsQuery = DB::table('jobs');
        if ($filterCompany) {
            $jobsQuery->where('company_name', $filterCompany);
        }
        $jobsQuery = $applyDateFilter($jobsQuery, $filterStartDate, $filterEndDate);

        $applicantsQuery = DB::table('users')->where('role', 'applicant');
        if ($filterStartDate) {
            $applicantsQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $applicantsQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $employeesQuery = DB::table('users')->where('role', 'employee');
        if ($filterStartDate) {
            $employeesQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $employeesQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $stats = [
            'jobs'       => $jobsQuery->count(),
            'applicants' => $applicantsQuery->count(),
            'employees'  => $employeesQuery->count(),
        ];

        // ==============================
        // APPLICATION PIPELINE FUNNEL
        // ==============================
        $pipelineQuery = Application::select('status', DB::raw('COUNT(*) as count'));

        if ($filterCompany) {
            $pipelineQuery->whereHas('job', function($q) use ($filterCompany) {
                $q->where('company_name', $filterCompany);
            });
        }

        if ($filterStartDate) {
            $pipelineQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $pipelineQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $pipelineFunnel = $pipelineQuery
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            })
            ->toArray();

        // ==============================
        // INTERVIEW STATISTICS
        // ==============================
        $interviewQuery = Interview::query();
        if ($filterStartDate) {
            $interviewQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $interviewQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $interviewStats = [
            'total' => (clone $interviewQuery)->count(),
            'scheduled' => (clone $interviewQuery)->where('status', 'scheduled')->count(),
            'rescheduled' => (clone $interviewQuery)->where('status', 'rescheduled')->count(),
            'completed' => (clone $interviewQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $interviewQuery)->where('status', 'cancelled')->count(),
        ];

        // ==============================
        // TRAINING & EVALUATION METRICS
        // ==============================
        $trainingQuery = TrainingSchedule::query();
        $evalQuery = TrainingEvaluation::query();

        if ($filterStartDate) {
            $trainingQuery->whereDate('created_at', '>=', $filterStartDate);
            $evalQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $trainingQuery->whereDate('created_at', '<=', $filterEndDate);
            $evalQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $totalEvaluations = (clone $evalQuery)->count();
        $trainingStats = [
            'total_trainings' => (clone $trainingQuery)->count(),
            'scheduled' => (clone $trainingQuery)->where('status', 'scheduled')->count(),
            'in_progress' => (clone $trainingQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $trainingQuery)->where('status', 'completed')->count(),
            'total_evaluations' => $totalEvaluations,
            'passed' => (clone $evalQuery)->where('result', 'Passed')->count(),
            'failed' => (clone $evalQuery)->where('result', 'Failed')->count(),
            'avg_score' => $totalEvaluations > 0 ? round((clone $evalQuery)->avg('total_score'), 1) : 0,
        ];

        // ==============================
        // TIME-TO-HIRE
        // ==============================
        $timeToHireQuery = DB::table('applications')
            ->where('status', 'hired')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at');

        if ($filterStartDate) {
            $timeToHireQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $timeToHireQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $timeToHireData = $timeToHireQuery->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')->first();
        $timeToHire = $timeToHireData && $timeToHireData->avg_days ? round($timeToHireData->avg_days, 1) : 0;

        // ==============================
        // JOB METRICS
        // ==============================
        $jobMetricsQuery = Job::query();
        if ($filterCompany) {
            $jobMetricsQuery->where('company_name', $filterCompany);
        }
        if ($filterStartDate) {
            $jobMetricsQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $jobMetricsQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $totalJobs = (clone $jobMetricsQuery)->count();
        $filledJobs = (clone $jobMetricsQuery)->where('status', 'filled')->count();

        $jobMetrics = [
            'active' => (clone $jobMetricsQuery)->where('status', 'active')->count(),
            'filled' => $filledJobs,
            'expired' => (clone $jobMetricsQuery)->where('status', 'expired')->count(),
            'fill_rate' => $totalJobs > 0 ? round(($filledJobs / $totalJobs) * 100, 1) : 0,
        ];

        // ==============================
        // TOP JOBS BY APPLICATIONS
        // ==============================
        $topJobsQuery = Job::withCount(['applications' => function($q) use ($filterStartDate, $filterEndDate) {
            if ($filterStartDate) {
                $q->whereDate('created_at', '>=', $filterStartDate);
            }
            if ($filterEndDate) {
                $q->whereDate('created_at', '<=', $filterEndDate);
            }
        }]);

        if ($filterCompany) {
            $topJobsQuery->where('company_name', $filterCompany);
        }
        if ($filterStartDate) {
            $topJobsQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $topJobsQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $topJobs = $topJobsQuery
            ->orderBy('applications_count', 'desc')
            ->limit(10)
            ->get();

        // ==============================
        // LEAVE FORMS
        // ==============================
        $leaveQuery = LeaveForm::select('status', DB::raw('COUNT(*) as total'));

        if ($filterStartDate) {
            $leaveQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $leaveQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $leaveCounts = $leaveQuery->groupBy('status')->pluck('total', 'status');

        $leaveData = [
            'pending'  => (int)($leaveCounts['Pending'] ?? $leaveCounts[0] ?? 0),
            'approved' => (int)($leaveCounts['Approved'] ?? $leaveCounts[1] ?? 0),
            'rejected' => (int)($leaveCounts['Declined'] ?? $leaveCounts[2] ?? 0),
        ];

        // Calculate hiring efficiency
        $hiringEfficiency = $stats['applicants'] > 0
            ? round(($stats['employees'] / $stats['applicants']) * 100, 1)
            : 0;

        // Calculate leave approval rate
        $totalLeaves = $leaveData['pending'] + $leaveData['approved'] + $leaveData['rejected'];
        $leaveApprovalRate = $totalLeaves > 0
            ? round(($leaveData['approved'] / $totalLeaves) * 100, 1)
            : 0;

        // Calculate training pass rate
        $trainingPassRate = $trainingStats['total_evaluations'] > 0
            ? round(($trainingStats['passed'] / $trainingStats['total_evaluations']) * 100, 1)
            : 0;

        // Prepare data for PDF
        $data = [
            'title' => 'HR Dashboard Report',
            'generated_date' => now()->format('F d, Y'),
            'generated_time' => now()->format('h:i A'),
            'filter_company' => $filterCompany ?? 'All Companies',
            'filter_start_date' => $filterStartDate ? date('F d, Y', strtotime($filterStartDate)) : 'N/A',
            'filter_end_date' => $filterEndDate ? date('F d, Y', strtotime($filterEndDate)) : 'N/A',
            'stats' => $stats,
            'pipelineFunnel' => $pipelineFunnel,
            'interviewStats' => $interviewStats,
            'trainingStats' => $trainingStats,
            'timeToHire' => $timeToHire,
            'jobMetrics' => $jobMetrics,
            'topJobs' => $topJobs,
            'leaveData' => $leaveData,
            'totalLeaves' => $totalLeaves,
            'hiringEfficiency' => $hiringEfficiency,
            'leaveApprovalRate' => $leaveApprovalRate,
            'trainingPassRate' => $trainingPassRate,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admins.hrAdmin.dashboard-report', $data);
        $pdf->setPaper('A4', 'portrait');

        // Download PDF
        $filename = 'HR_Dashboard_Report_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }
}
