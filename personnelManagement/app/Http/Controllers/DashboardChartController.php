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
use Carbon\Carbon;

class DashboardChartController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $filterCompany = $request->input('company');
        $filterStartDate = $request->input('start_date');
        $filterEndDate = $request->input('end_date');

        // Labels for months
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Get distinct company names from jobs table (sanitized)
        $companiesQuery = DB::table('jobs')->distinct();

        // Apply company filter if selected
        if ($filterCompany) {
            $companiesQuery->where('company_name', $filterCompany);
        }

        $companies = $companiesQuery
            ->pluck('company_name')
            ->filter(function($name) {
                // Ensure only valid company names (extra safety layer)
                return !empty($name) && is_string($name);
            });

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

        // Helper function: Fill missing months with zeros
        $fillMonths = function ($counts) {
            $monthlyData = array_fill(0, 12, 0);
            foreach ($counts as $row) {
                $monthIndex = (int)$row->month - 1;
                if ($monthIndex >= 0 && $monthIndex < 12) {
                    $monthlyData[$monthIndex] = (int)$row->total;
                }
            }
            return $monthlyData;
        };

        // ==============================
        // JOB DATA (per company from jobs table)
        // ==============================
        $jobData = [];
        foreach ($companies as $company) {
            // Safe: $company is from database, WHERE uses parameter binding
            $query = DB::table('jobs')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->where('company_name', $company);  // Parameter binding prevents SQL injection

            // Apply date filters
            $query = $applyDateFilter($query, $filterStartDate, $filterEndDate);

            $counts = $query->groupBy('month')
                ->orderBy('month')
                ->get();

            $jobData[$company] = $fillMonths($counts);
        }

        // ==============================
        // APPLICANTS (per company from applications table)
        // ==============================
        $applicantData = [];
        foreach ($companies as $company) {
            $query = DB::table('applications')
                ->join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('users', 'applications.user_id', '=', 'users.id')
                ->selectRaw('MONTH(applications.created_at) as month, COUNT(*) as total')
                ->where('users.role', 'applicant')
                ->where('jobs.company_name', $company);

            // Apply date filters
            if ($filterStartDate) {
                $query->whereDate('applications.created_at', '>=', $filterStartDate);
            }
            if ($filterEndDate) {
                $query->whereDate('applications.created_at', '<=', $filterEndDate);
            }

            $counts = $query->groupBy('month')
                ->orderBy('month')
                ->get();

            $applicantData[$company] = $fillMonths($counts);
        }

        // ==============================
        // EMPLOYEES (per company from applications table)
        // ==============================
        $employeeData = [];
        foreach ($companies as $company) {
            $query = DB::table('applications')
                ->join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('users', 'applications.user_id', '=', 'users.id')
                ->selectRaw('MONTH(applications.created_at) as month, COUNT(*) as total')
                ->where('users.role', 'employee')
                ->where('jobs.company_name', $company);

            // Apply date filters
            if ($filterStartDate) {
                $query->whereDate('applications.created_at', '>=', $filterStartDate);
            }
            if ($filterEndDate) {
                $query->whereDate('applications.created_at', '<=', $filterEndDate);
            }

            $counts = $query->groupBy('month')
                ->orderBy('month')
                ->get();

            $employeeData[$company] = $fillMonths($counts);
        }

        // ==============================
        // LEAVE FORMS (group by status)
        // ==============================
        $leaveQuery = LeaveForm::select('status', DB::raw('COUNT(*) as total'));

        // Apply date filters to leave forms
        if ($filterStartDate) {
            $leaveQuery->whereDate('created_at', '>=', $filterStartDate);
        }
        if ($filterEndDate) {
            $leaveQuery->whereDate('created_at', '<=', $filterEndDate);
        }

        $leaveCounts = $leaveQuery->groupBy('status')->pluck('total', 'status');

        // Normalize leave data (support both string + numeric status codes, always return integers)
        $leaveData = [
            'pending'  => (int)(isset($leaveCounts['Pending']) ? $leaveCounts['Pending'] : (isset($leaveCounts[0]) ? $leaveCounts[0] : 0)),
            'approved' => (int)(isset($leaveCounts['Approved']) ? $leaveCounts['Approved'] : (isset($leaveCounts[1]) ? $leaveCounts[1] : 0)),
            'rejected' => (int)(isset($leaveCounts['Declined']) ? $leaveCounts['Declined'] : (isset($leaveCounts[2]) ? $leaveCounts[2] : 0)),
        ];

        // ==============================
        // Final Chart Data
        // ==============================
        $chartData = [
            'labels'     => $labels,
            'companies'  => $companies,
            'job'        => $jobData,
            'applicants' => $applicantData,
            'employee'   => $employeeData
        ];

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

        // Apply company filter
        if ($filterCompany) {
            $pipelineQuery->whereHas('job', function($q) use ($filterCompany) {
                $q->where('company_name', $filterCompany);
            });
        }

        // Apply date filters
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
                // Extract the string value from the enum object
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
            'upcoming_7days' => (clone $interviewQuery)->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->where('scheduled_at', '<=', now()->addDays(7))
                ->count(),
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
            'passed' => (clone $evalQuery)->where('result', 'passed')->count(),
            'failed' => (clone $evalQuery)->where('result', 'failed')->count(),
            'avg_score' => $totalEvaluations > 0 ? round((clone $evalQuery)->avg('total_score'), 1) : 0,
        ];

        // ==============================
        // TIME-TO-HIRE (Average days from application to hire)
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
            'expiring_soon' => (clone $jobMetricsQuery)->where('apply_until', '<=', now()->addDays(7))
                ->where('apply_until', '>=', now())
                ->where('status', 'active')
                ->count(),
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

        $topJobsData = $topJobsQuery
            ->orderBy('applications_count', 'desc')
            ->limit(5)
            ->get();

        $topJobs = [];
        foreach ($topJobsData as $job) {
            $topJobs[] = [
                'title' => isset($job->job_title) ? $job->job_title : 'Untitled',
                'company' => isset($job->company_name) ? $job->company_name : 'Unknown',
                'count' => isset($job->applications_count) ? $job->applications_count : 0
            ];
        }

        // Ensure all data has proper defaults
        $pipelineFunnel = isset($pipelineFunnel) ? $pipelineFunnel : [];
        $interviewStats = array_merge([
            'total' => 0,
            'scheduled' => 0,
            'rescheduled' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'upcoming_7days' => 0
        ], isset($interviewStats) ? $interviewStats : []);

        $trainingStats = array_merge([
            'total_trainings' => 0,
            'scheduled' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'total_evaluations' => 0,
            'passed' => 0,
            'failed' => 0,
            'avg_score' => 0
        ], isset($trainingStats) ? $trainingStats : []);

        $jobMetrics = array_merge([
            'active' => 0,
            'filled' => 0,
            'expired' => 0,
            'expiring_soon' => 0,
            'fill_rate' => 0
        ], isset($jobMetrics) ? $jobMetrics : []);

        $topJobs = isset($topJobs) ? $topJobs : [];

        // Get notifications for admin
        $notifications = $this->getAdminNotifications();

        return view('admins.hrAdmin.dashboard', compact(
            'chartData',
            'stats',
            'leaveData',
            'pipelineFunnel',
            'interviewStats',
            'trainingStats',
            'timeToHire',
            'jobMetrics',
            'topJobs',
            'notifications'
        ));
    }

    /**
     * Get notifications for admin dashboard
     */
    private function getAdminNotifications()
    {
        $notifications = [];
        $today = Carbon::today();

        // 1. Pending Applications (needs approval)
        $pendingAppsCount = Application::where('status', 'pending')->count();
        if ($pendingAppsCount > 0) {
            $notifications[] = [
                'type' => 'application',
                'title' => 'Applications Pending Review',
                'message' => "You have {$pendingAppsCount} application(s) waiting for approval",
                'details' => [
                    'Pending Count' => $pendingAppsCount,
                ],
                'action_url' => route('hrAdmin.application'),
                'action_text' => 'Review Applications'
            ];
        }

        // 2. Upcoming Interviews (within next 7 days)
        $upcomingInterviews = Interview::with(['application.job', 'applicant'])
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$today, $today->copy()->addDays(7)])
            ->orderBy('scheduled_at')
            ->get();

        foreach ($upcomingInterviews as $interview) {
            $interviewDate = Carbon::parse($interview->scheduled_at);
            $daysUntil = $today->diffInDays($interviewDate, false);

            $jobTitle = isset($interview->application->job->job_title) ? $interview->application->job->job_title : 'N/A';
            $notifications[] = [
                'type' => 'interview',
                'title' => 'Upcoming Interview',
                'message' => "{$interview->applicant->first_name} {$interview->applicant->last_name} - {$jobTitle}",
                'days_until' => (int)$daysUntil,
                'details' => [
                    'Date' => $interviewDate->format('M d, Y'),
                    'Time' => $interviewDate->format('h:i A'),
                ],
                'action_url' => route('hrAdmin.interviews.index'),
                'action_text' => 'View Schedule'
            ];
        }

        // 3. Approved Applications without Interview (needs scheduling)
        $approvedNoInterview = Application::with(['job', 'user'])
            ->where('status', 'approved')
            ->whereDoesntHave('interview')
            ->get();

        foreach ($approvedNoInterview as $application) {
            $notifications[] = [
                'type' => 'application',
                'title' => 'Schedule Interview',
                'message' => "{$application->user->first_name} {$application->user->last_name} - {$application->job->job_title}",
                'details' => [
                    'Company' => $application->job->company_name,
                ],
                'action_url' => route('hrAdmin.applicants', ['id' => $application->job_id]),
                'action_text' => 'Schedule Now'
            ];
        }

        // 4. Upcoming Training Sessions (within next 7 days)
        $upcomingTraining = TrainingSchedule::with(['application.job', 'application.user'])
            ->where('status', 'scheduled')
            ->whereBetween('start_date', [$today, $today->copy()->addDays(7)])
            ->orderBy('start_date')
            ->get();

        foreach ($upcomingTraining as $training) {
            $startDate = Carbon::parse($training->start_date);
            $daysUntil = $today->diffInDays($startDate, false);

            $trainingJobTitle = isset($training->application->job->job_title) ? $training->application->job->job_title : 'N/A';
            $notifications[] = [
                'type' => 'training',
                'title' => 'Upcoming Training',
                'message' => "{$training->application->user->first_name} {$training->application->user->last_name} - {$trainingJobTitle}",
                'days_until' => (int)$daysUntil,
                'details' => [
                    'Starts' => $startDate->format('M d, Y'),
                    'Duration' => $startDate->diffInDays(Carbon::parse($training->end_date)) . ' days',
                ],
                'action_url' => route('hrAdmin.application'),
                'action_text' => 'View Details'
            ];
        }

        // 5. Trained Applications Pending Evaluation
        $pendingEvaluation = Application::with(['job', 'user'])
            ->where('status', 'trained')
            ->whereDoesntHave('evaluation')
            ->get();

        foreach ($pendingEvaluation as $application) {
            $notifications[] = [
                'type' => 'evaluation',
                'title' => 'Needs Evaluation',
                'message' => "{$application->user->first_name} {$application->user->last_name} - {$application->job->job_title}",
                'details' => [
                    'Company' => $application->job->company_name,
                    'Status' => 'Training completed',
                ],
                'action_url' => route('hrAdmin.applicants', ['id' => $application->job_id]),
                'action_text' => 'Evaluate Now'
            ];
        }

        // Sort notifications by urgency (days_until ascending)
        usort($notifications, function($a, $b) {
            $aDays = isset($a['days_until']) ? $a['days_until'] : 999;
            $bDays = isset($b['days_until']) ? $b['days_until'] : 999;
            return $aDays <=> $bDays;
        });

        return $notifications;
    }
}
