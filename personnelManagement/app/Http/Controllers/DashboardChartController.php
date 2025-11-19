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
use App\Models\User;
use App\Models\ContractInvitation;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'passed' => (clone $evalQuery)->where('result', 'Passed')->count(),
            'failed' => (clone $evalQuery)->where('result', 'Failed')->count(),
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

        // Get database notifications and unread count
        $dbNotifications = $this->getFormattedNotifications();
        $unreadCount = auth()->user()->unreadNotifications->count();

        // Merge with dynamic notifications (for transition period)
        $allNotifications = array_merge($dbNotifications, $notifications);

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
            'notifications',
            'allNotifications',
            'unreadCount'
        ));
    }

    /**
     * Get notifications for admin dashboard - Grouped by job posting
     */
    private function getAdminNotifications()
    {
        $notifications = [];
        $today = Carbon::today();

        // Get all active jobs with pending actions
        $jobs = Job::with([
            'applications' => function($query) {
                $query->where('is_archived', false);
            },
            'applications.interview',
            'applications.trainingSchedule',
            'applications.evaluation',
            'applications.user'
        ])->get();

        // Group notifications by job
        foreach ($jobs as $job) {
            $jobId = $job->id;
            $jobTitle = $job->job_title;
            $company = $job->company_name;

            // Count different pending actions for this job
            $pendingCount = 0;
            $needInterviewCount = 0;
            $upcomingInterviewsCount = 0;
            $upcomingTrainingCount = 0;
            $pendingEvaluationCount = 0;

            // Track urgency for this job
            $hasUrgentAction = false;
            $minDaysUntil = 999;
            $urgentDetails = [];

            foreach ($job->applications as $application) {
                // 1. Pending applications
                if ($application->status->value === 'pending') {
                    $pendingCount++;
                }

                // 2. Approved but no interview scheduled
                if ($application->status->value === 'approved' && !$application->interview) {
                    $needInterviewCount++;
                }

                // 3. Upcoming interviews (within 7 days)
                if ($application->interview &&
                    $application->interview->status === 'scheduled' &&
                    Carbon::parse($application->interview->scheduled_at)->between($today, $today->copy()->addDays(7))) {

                    $upcomingInterviewsCount++;
                    $interviewDate = Carbon::parse($application->interview->scheduled_at);
                    $daysUntil = $today->diffInDays($interviewDate, false);

                    // Check if urgent (≤2 days)
                    if ($daysUntil <= 2) {
                        $hasUrgentAction = true;
                        $minDaysUntil = min($minDaysUntil, $daysUntil);

                        $timeInfo = $daysUntil == 0 ? 'Today' : ($daysUntil == 1 ? 'Tomorrow' : $interviewDate->format('M d'));
                        $timeInfo .= ' at ' . $interviewDate->format('g:i A');

                        $urgentDetails[] = [
                            'type' => 'interview',
                            'applicant' => "{$application->user->first_name} {$application->user->last_name}",
                            'time' => $timeInfo,
                            'days_until' => $daysUntil
                        ];
                    }
                }

                // 4. Needs training schedule (interviewed but no training scheduled)
                if ($application->status->value === 'scheduled_for_training' && !$application->trainingSchedule) {
                    $upcomingTrainingCount++;
                }

                // 5. Pending evaluations
                if ($application->status->value === 'trained' && !$application->evaluation) {
                    $pendingEvaluationCount++;
                }
            }

            // Determine total actions count and priority tab
            $totalActions = $pendingCount + $needInterviewCount + $upcomingInterviewsCount + $upcomingTrainingCount + $pendingEvaluationCount;

            // If this job has pending actions
            if ($totalActions > 0) {
                // If urgent actions exist (≤2 days), create individual notifications
                if ($hasUrgentAction) {
                    foreach ($urgentDetails as $urgentAction) {
                        $tab = $urgentAction['type'] === 'interview' ? 'interview' : 'training';

                        $notifications[] = [
                            'type' => $urgentAction['type'],
                            'title' => $urgentAction['type'] === 'interview' ? 'Urgent Interview' : 'Urgent Training',
                            'message' => "{$jobTitle} ({$company}): {$urgentAction['applicant']} • {$urgentAction['time']}",
                            'days_until' => $urgentAction['days_until'],
                            'action_url' => route('hrAdmin.applicants', ['id' => $jobId, 'tab' => $tab]),
                            'action_text' => 'View Details'
                        ];
                    }
                } else {
                    // Create grouped notification for non-urgent actions
                    $messageParts = [];
                    $priorityTab = 'applicants'; // Default tab

                    if ($pendingCount > 0) {
                        $messageParts[] = "{$pendingCount} " . ($pendingCount == 1 ? 'applicant' : 'applicants') . " pending";
                        $priorityTab = 'applicants';
                    }

                    if ($needInterviewCount > 0) {
                        $messageParts[] = "{$needInterviewCount} need interview schedule";
                        if ($priorityTab === 'applicants') {
                            $priorityTab = 'interview';
                        }
                    }

                    if ($upcomingInterviewsCount > 0) {
                        $messageParts[] = "{$upcomingInterviewsCount} upcoming " . ($upcomingInterviewsCount == 1 ? 'interview' : 'interviews');
                        if ($priorityTab === 'applicants') {
                            $priorityTab = 'interview';
                        }
                    }

                    if ($upcomingTrainingCount > 0) {
                        $messageParts[] = "{$upcomingTrainingCount} need training schedule";
                        if (in_array($priorityTab, ['applicants', 'interview'])) {
                            $priorityTab = 'training';
                        }
                    }

                    if ($pendingEvaluationCount > 0) {
                        $messageParts[] = "{$pendingEvaluationCount} pending " . ($pendingEvaluationCount == 1 ? 'evaluation' : 'evaluations');
                        if (in_array($priorityTab, ['applicants', 'interview', 'training'])) {
                            $priorityTab = 'evaluation';
                        }
                    }

                    $message = "{$jobTitle} ({$company}): " . implode(', ', $messageParts);

                    // Determine notification type based on what actions are present
                    $notificationType = 'application';
                    if ($upcomingInterviewsCount > 0 || $needInterviewCount > 0) {
                        $notificationType = 'interview';
                    } elseif ($upcomingTrainingCount > 0) {
                        $notificationType = 'training';
                    } elseif ($pendingEvaluationCount > 0) {
                        $notificationType = 'evaluation';
                    }

                    $notifications[] = [
                        'type' => $notificationType,
                        'title' => 'Pending Actions',
                        'message' => $message,
                        'action_url' => route('hrAdmin.applicants', ['id' => $jobId, 'tab' => $priorityTab]),
                        'action_text' => 'Review'
                    ];
                }
            }
        }

        // Sort notifications by urgency (days_until ascending, urgent first)
        usort($notifications, function($a, $b) {
            $aDays = isset($a['days_until']) ? $a['days_until'] : 999;
            $bDays = isset($b['days_until']) ? $b['days_until'] : 999;
            return $aDays <=> $bDays;
        });

        return $notifications;
    }

    /**
     * HR Staff Dashboard
     */
    public function hrStaffDashboard()
    {
        // Get counts for stat cards
        $interviewScheduleCount = Application::whereHas('interview', function($query) {
            $query->where('status', 'scheduled');
        })->where('is_archived', false)->count();

        $trainingScheduleCount = Application::whereHas('trainingSchedule', function($query) {
            $query->where('status', 'scheduled');
        })->where('is_archived', false)->count();

        $pendingEvaluationCount = Application::where('status', 'trained')
            ->whereDoesntHave('evaluation')
            ->where('is_archived', false)
            ->count();

        // ==============================
        // CURRENT MONTH ANALYTICS
        // ==============================
        $currentMonthEvals = TrainingEvaluation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        $totalCurrentMonth = (clone $currentMonthEvals)->count();
        $passedCurrentMonth = (clone $currentMonthEvals)->where('result', 'Passed')->count();

        $averageEvaluationScore = round((clone $currentMonthEvals)->avg('total_score') ?? 0, 1);
        $passRateThisMonth = $totalCurrentMonth > 0 ? round(($passedCurrentMonth / $totalCurrentMonth) * 100, 1) : 0;

        $promotionsThisMonth = Application::where('status', 'hired')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // ==============================
        // PROMOTION PIPELINE STATS (All Time)
        // ==============================
        // Include archived applications with evaluations (failed applicants are auto-archived)
        $pipelineStats = [
            'evaluated' => Application::whereHas('evaluation')
                ->where(function($q) {
                    $q->where('is_archived', false)->orWhereHas('evaluation');
                })
                ->count(),
            'passed' => Application::whereHas('evaluation', function($q) {
                $q->where('result', 'Passed');
            })
                ->whereIn('status', ['trained', 'passed_evaluation'])
                ->where(function($q) {
                    $q->where('is_archived', false)->orWhereHas('evaluation');
                })
                ->count(),
            'invited' => Application::whereHas('contractInvitations')
                ->where('is_archived', false)
                ->distinct()
                ->count('id'),
            'promoted' => Application::where('status', 'hired')->count()
        ];

        // ==============================
        // TRAINING & EVALUATION OVERVIEW (All Time)
        // ==============================
        $totalEvals = TrainingEvaluation::count();
        $passedEvals = TrainingEvaluation::where('result', 'Passed')->count();

        $trainingStats = [
            'passed' => $passedEvals,
            'failed' => TrainingEvaluation::where('result', 'Failed')->count(),
            'total_evaluations' => $totalEvals,
            'avg_score' => round(TrainingEvaluation::avg('total_score') ?? 0, 1),
            'pass_rate' => $totalEvals > 0 ? round(($passedEvals / $totalEvals) * 100, 1) : 0,
            'avg_knowledge' => round(TrainingEvaluation::avg('knowledge') ?? 0, 1),
            'avg_skill' => round(TrainingEvaluation::avg('skill') ?? 0, 1),
            'avg_participation' => round(TrainingEvaluation::avg('participation') ?? 0, 1),
            'avg_professionalism' => round(TrainingEvaluation::avg('professionalism') ?? 0, 1)
        ];

        // ==============================
        // YEAR/MONTH OPTIONS FOR FILTERS
        // ==============================
        // Get distinct years from applications and evaluations
        $applicationYears = Application::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $evaluationYears = TrainingEvaluation::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $years = $applicationYears->merge($evaluationYears)->unique()->sort()->reverse()->values();

        // If no data, include current year
        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Get notifications for HR Staff
        $notifications = $this->getHrStaffNotifications();

        // Get database notifications and unread count
        $dbNotifications = $this->getFormattedNotifications();
        $unreadCount = auth()->user()->unreadNotifications->count();

        // Merge with dynamic notifications (for transition period)
        $allNotifications = array_merge($dbNotifications, $notifications);

        // Get companies and positions for dropdowns
        $companies = Job::distinct()->pluck('company_name')->filter()->values();
        $positions = Job::distinct()->pluck('job_title')->filter()->values();

        // ==============================
        // CONTRACT INVITATION STATISTICS
        // ==============================
        $invitationStats = [
            // Total invitations sent (all time)
            'total_sent' => ContractInvitation::count(),

            // Invitations sent today
            'sent_today' => ContractInvitation::whereDate('sent_at', now()->toDateString())->count(),

            // Invitations sent this week
            'sent_this_week' => ContractInvitation::whereBetween('sent_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),

            // Invitations sent this month
            'sent_this_month' => ContractInvitation::whereMonth('sent_at', now()->month)
                ->whereYear('sent_at', now()->year)
                ->count(),

            // Unique applicants with invitations
            'unique_applicants' => ContractInvitation::distinct('application_id')->count('application_id'),

            // Successful email deliveries
            'emails_sent' => ContractInvitation::where('email_sent', true)->count(),

            // Failed email deliveries
            'emails_failed' => ContractInvitation::where('email_sent', false)->count(),

            // Recent invitations (last 7 days)
            'recent' => ContractInvitation::with(['application.user', 'application.job', 'sentBy'])
                ->where('sent_at', '>=', now()->subDays(7))
                ->orderBy('sent_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($invitation) {
                    return [
                        'applicant_name' => $invitation->application->user->full_name ?? 'N/A',
                        'position' => $invitation->application->job->job_title ?? 'N/A',
                        'company' => $invitation->application->job->company_name ?? 'N/A',
                        'scheduled_date' => $invitation->contract_date,
                        'scheduled_time' => $invitation->contract_signing_time,
                        'sent_at' => $invitation->sent_at->format('M d, Y H:i A'),
                        'sent_by' => $invitation->sentBy->full_name ?? 'Unknown',
                        'email_sent' => $invitation->email_sent
                    ];
                })
        ];

        return view('admins.hrStaff.dashboard', compact(
            'interviewScheduleCount',
            'trainingScheduleCount',
            'pendingEvaluationCount',
            'averageEvaluationScore',
            'passRateThisMonth',
            'promotionsThisMonth',
            'pipelineStats',
            'trainingStats',
            'invitationStats',
            'years',
            'currentYear',
            'currentMonth',
            'notifications',
            'allNotifications',
            'unreadCount',
            'companies',
            'positions'
        ));
    }

    /**
     * Get notifications for HR Staff dashboard
     */
    private function getHrStaffNotifications()
    {
        $notifications = [];
        $today = Carbon::today();

        // Get applications with necessary relationships
        $applications = Application::with([
            'job',
            'user',
            'trainingSchedule',
            'evaluation'
        ])
        ->where('is_archived', false)
        ->get();

        foreach ($applications as $application) {
            $applicantName = "{$application->user->first_name} {$application->user->last_name}";
            $position = $application->job->job_title;
            $company = $application->job->company_name;

            // 1. URGENT: Training ended >7 days ago, not evaluated (RED)
            if ($application->trainingSchedule &&
                $application->trainingSchedule->status === 'completed' &&
                !$application->evaluation) {

                $endDate = Carbon::parse($application->trainingSchedule->end_date);
                $daysOverdue = $today->diffInDays($endDate, false);

                if ($daysOverdue >= 7) {
                    $notifications[] = [
                        'priority' => 'urgent',
                        'type' => 'evaluation_overdue',
                        'title' => 'Evaluation Overdue',
                        'applicant_name' => $applicantName,
                        'position' => $position,
                        'company' => $company,
                        'message' => "Training completed {$daysOverdue} days ago - Evaluation overdue",
                        'action_url' => route('hrStaff.perfEval'),
                        'action_text' => 'Evaluate Now',
                        'days_metric' => $daysOverdue
                    ];
                }
            }

            // 2. IMPORTANT: Training ending within 3 days (AMBER)
            if ($application->trainingSchedule &&
                $application->trainingSchedule->status === 'scheduled') {

                $endDate = Carbon::parse($application->trainingSchedule->end_date);
                $daysUntilEnd = $today->diffInDays($endDate, false);

                if ($daysUntilEnd >= 0 && $daysUntilEnd <= 3) {
                    $timeText = $daysUntilEnd == 0 ? 'Ends Today' :
                               ($daysUntilEnd == 1 ? 'Ends Tomorrow' :
                               "Ends in {$daysUntilEnd} days");

                    $notifications[] = [
                        'priority' => 'important',
                        'type' => 'training_ending_soon',
                        'title' => 'Training Ending Soon',
                        'applicant_name' => $applicantName,
                        'position' => $position,
                        'company' => $company,
                        'message' => "{$timeText} - Prepare evaluation",
                        'action_url' => route('hrStaff.perfEval'),
                        'action_text' => 'View Training',
                        'days_metric' => $daysUntilEnd
                    ];
                }
            }

            // 3. INFO: Passed applicants awaiting invitation (BLUE)
            if ($application->evaluation &&
                $application->evaluation->result === 'passed' &&
                $application->status->value === 'trained') {

                $notifications[] = [
                    'priority' => 'info',
                    'type' => 'passed_awaiting_invitation',
                    'title' => 'Ready for Invitation',
                    'applicant_name' => $applicantName,
                    'position' => $position,
                    'company' => $company,
                    'message' => "Evaluation passed ({$application->evaluation->total_score}%) - Ready for invitation",
                    'action_url' => route('hrStaff.perfEval'),
                    'action_text' => 'Send Invitation',
                    'days_metric' => null
                ];
            }
        }

        // Sort by priority and days metric
        usort($notifications, function($a, $b) {
            $priorityOrder = ['urgent' => 1, 'important' => 2, 'info' => 3];
            $aPriority = $priorityOrder[$a['priority']] ?? 999;
            $bPriority = $priorityOrder[$b['priority']] ?? 999;

            if ($aPriority !== $bPriority) {
                return $aPriority <=> $bPriority;
            }

            // Within same priority, sort by days metric (higher is more urgent for overdue, lower for upcoming)
            $aDays = $a['days_metric'] ?? 0;
            $bDays = $b['days_metric'] ?? 0;

            if ($a['priority'] === 'urgent') {
                return $bDays <=> $aDays; // Descending for overdue
            } else {
                return $aDays <=> $bDays; // Ascending for upcoming
            }
        });

        return $notifications;
    }

    /**
     * Get positions by company (AJAX)
     */
    public function getPositionsByCompany(Request $request)
    {
        $company = $request->input('company');

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company parameter is required',
                'positions' => []
            ], 400);
        }

        // Get distinct job positions for the selected company
        $positions = Job::where('company_name', $company)
            ->distinct()
            ->pluck('job_title')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'positions' => $positions
        ]);
    }

    /**
     * Filter applications for HR Staff (AJAX)
     */
    public function filterApplications(Request $request)
    {
        // Build filtered query
        // NOTE: Include archived applications if they have evaluations (to show failed applicants)
        $query = Application::with(['job', 'user', 'evaluation'])
            ->where(function($q) {
                $q->where('is_archived', false)
                  ->orWhereHas('evaluation'); // Include archived apps with evaluations (failed applicants)
            });

        // Filter by company
        if ($request->filled('company')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('company_name', $request->input('company'));
            });
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('job_title', $request->input('position'));
            });
        }

        // Filter by evaluation status
        $evalStatus = $request->input('evaluation_status');
        if ($evalStatus === 'passed') {
            $query->whereHas('evaluation', function($q) {
                $q->where('result', 'Passed');
            });
        } elseif ($evalStatus === 'failed') {
            $query->whereHas('evaluation', function($q) {
                $q->where('result', 'Failed');
            });
        } elseif ($evalStatus === 'pending') {
            $query->where('status', 'trained')->whereDoesntHave('evaluation');
        }

        // Filter by year and month
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        // Filter by score range
        if ($request->filled('min_score')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->where('total_score', '>=', $request->input('min_score'));
            });
        }
        if ($request->filled('max_score')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->where('total_score', '<=', $request->input('max_score'));
            });
        }

        // Get filtered applications
        $filteredApplications = $query->get();

        // Calculate filtered pipeline stats
        $filteredPipelineStats = [
            'evaluated' => $filteredApplications->filter(function($app) {
                return $app->evaluation !== null;
            })->count(),
            'passed' => $filteredApplications->filter(function($app) {
                return $app->evaluation && $app->evaluation->result === 'Passed';
            })->whereIn('status', ['trained', 'passed_evaluation'])->count(),
            'invited' => $filteredApplications->where('status', 'invited')->count(),
            'promoted' => $filteredApplications->where('status', 'hired')->count()
        ];

        // Get filtered evaluations for training stats
        $filteredEvaluations = $filteredApplications->filter(function($app) {
            return $app->evaluation !== null;
        })->pluck('evaluation');

        $totalFilteredEvals = $filteredEvaluations->count();
        $passedFilteredEvals = $filteredEvaluations->where('result', 'Passed')->count();

        // Calculate filtered training stats
        $filteredTrainingStats = [
            'passed' => $passedFilteredEvals,
            'failed' => $filteredEvaluations->where('result', 'Failed')->count(),
            'total_evaluations' => $totalFilteredEvals,
            'avg_score' => $totalFilteredEvals > 0 ? round($filteredEvaluations->avg('total_score'), 1) : 0,
            'pass_rate' => $totalFilteredEvals > 0 ? round(($passedFilteredEvals / $totalFilteredEvals) * 100, 1) : 0,
            'avg_knowledge' => $totalFilteredEvals > 0 ? round($filteredEvaluations->avg('knowledge'), 1) : 0,
            'avg_skill' => $totalFilteredEvals > 0 ? round($filteredEvaluations->avg('skill'), 1) : 0,
            'avg_participation' => $totalFilteredEvals > 0 ? round($filteredEvaluations->avg('participation'), 1) : 0,
            'avg_professionalism' => $totalFilteredEvals > 0 ? round($filteredEvaluations->avg('professionalism'), 1) : 0
        ];

        // Calculate OVERALL stats (all-time data for comparison)
        // Include archived applications with evaluations (failed applicants are auto-archived)
        $allApplications = Application::with(['evaluation'])
            ->where(function($q) {
                $q->where('is_archived', false)
                  ->orWhereHas('evaluation');
            })
            ->get();

        $overallPipelineStats = [
            'evaluated' => $allApplications->filter(function($app) {
                return $app->evaluation !== null;
            })->count(),
            'passed' => $allApplications->filter(function($app) {
                return $app->evaluation && $app->evaluation->result === 'Passed';
            })->whereIn('status', ['trained', 'passed_evaluation'])->count(),
            'invited' => $allApplications->where('status', 'invited')->count(),
            'promoted' => $allApplications->where('status', 'hired')->count()
        ];

        $allEvaluations = TrainingEvaluation::all();
        $totalOverallEvals = $allEvaluations->count();
        $passedOverallEvals = $allEvaluations->where('result', 'Passed')->count();

        $overallTrainingStats = [
            'passed' => $passedOverallEvals,
            'failed' => $allEvaluations->where('result', 'Failed')->count(),
            'total_evaluations' => $totalOverallEvals,
            'avg_score' => $totalOverallEvals > 0 ? round($allEvaluations->avg('total_score'), 1) : 0,
            'pass_rate' => $totalOverallEvals > 0 ? round(($passedOverallEvals / $totalOverallEvals) * 100, 1) : 0,
            'avg_knowledge' => $totalOverallEvals > 0 ? round($allEvaluations->avg('knowledge'), 1) : 0,
            'avg_skill' => $totalOverallEvals > 0 ? round($allEvaluations->avg('skill'), 1) : 0,
            'avg_participation' => $totalOverallEvals > 0 ? round($allEvaluations->avg('participation'), 1) : 0,
            'avg_professionalism' => $totalOverallEvals > 0 ? round($allEvaluations->avg('professionalism'), 1) : 0
        ];

        // Format application data for display
        $applicationData = $filteredApplications->map(function($app) {
            return [
                'id' => $app->id,
                'applicant_name' => "{$app->user->first_name} {$app->user->last_name}",
                'position' => $app->job->job_title,
                'company' => $app->job->company_name,
                'status' => $app->status->value,
                'evaluation_score' => $app->evaluation ? $app->evaluation->total_score : null,
                'evaluation_result' => $app->evaluation ? $app->evaluation->result : 'pending',
                'applied_date' => $app->created_at->format('M d, Y')
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $filteredApplications->count(),
            'filtered' => [
                'pipelineStats' => $filteredPipelineStats,
                'trainingStats' => $filteredTrainingStats
            ],
            'overall' => [
                'pipelineStats' => $overallPipelineStats,
                'trainingStats' => $overallTrainingStats
            ],
            'data' => $applicationData
        ]);
    }

    /**
     * Generate PDF report for HR Staff
     * NOTE: By default, includes ALL evaluated applicants (both Passed and Failed)
     * unless evaluation_status filter is specifically applied
     */
    public function generateReport(Request $request, $type)
    {
        // Build query based on filters (same logic as filterApplications)
        // IMPORTANT: This query includes ALL applications (passed and failed) by default
        // Include archived applications if they have evaluations (failed applicants are auto-archived)
        $query = Application::with(['job', 'user', 'evaluation', 'trainingSchedule'])
            ->where(function($q) {
                $q->where('is_archived', false)
                  ->orWhereHas('evaluation'); // Include archived apps with evaluations (failed applicants)
            });

        // Apply filters
        if ($request->filled('company')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('company_name', $request->input('company'));
            });
        }

        if ($request->filled('position')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('job_title', $request->input('position'));
            });
        }

        // Filter by evaluation status (optional)
        // If not provided, includes ALL applicants with evaluations (Passed AND Failed)
        if ($request->filled('evaluation_status')) {
            $evalStatus = $request->input('evaluation_status');
            if ($evalStatus === 'passed') {
                $query->whereHas('evaluation', function($q) {
                    $q->where('result', 'Passed');
                });
            } elseif ($evalStatus === 'failed') {
                $query->whereHas('evaluation', function($q) {
                    $q->where('result', 'Failed');
                });
            } elseif ($evalStatus === 'pending') {
                $query->where('status', 'trained')->whereDoesntHave('evaluation');
            }
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        if ($request->filled('min_score')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->where('total_score', '>=', $request->input('min_score'));
            });
        }
        if ($request->filled('max_score')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->where('total_score', '<=', $request->input('max_score'));
            });
        }

        $applications = $query->get();

        // Prepare data based on report type
        $reportData = [
            'type' => $type,
            'generated_date' => now()->format('F d, Y'),
            'applications' => $applications,
            'filters' => $request->all()
        ];

        // Generate statistics based on report type
        switch ($type) {
            case 'training-evaluation':
                $evaluatedApps = $applications->filter(function($app) {
                    return $app->evaluation !== null;
                });

                $reportData['stats'] = [
                    'total_evaluations' => $evaluatedApps->count(),
                    'passed' => $evaluatedApps->filter(function($app) {
                        return $app->evaluation && $app->evaluation->result === 'Passed';
                    })->count(),
                    'failed' => $evaluatedApps->filter(function($app) {
                        return $app->evaluation && $app->evaluation->result === 'Failed';
                    })->count(),
                    'avg_score' => $evaluatedApps->count() > 0 ? $evaluatedApps->avg(function($app) {
                        return $app->evaluation ? $app->evaluation->total_score : 0;
                    }) : 0
                ];
                break;

            case 'employee-promotion':
                $reportData['stats'] = [
                    'total_promoted' => $applications->where('status.value', 'hired')->count(),
                    'pending_promotion' => $applications->whereIn('status.value', ['trained', 'passed_evaluation'])->count()
                ];
                break;

            case 'requirements-status':
                $reportData['stats'] = [
                    'total_applications' => $applications->count(),
                    'with_interview' => $applications->where('interview', '!=', null)->count(),
                    'with_training' => $applications->where('trainingSchedule', '!=', null)->count(),
                    'with_evaluation' => $applications->where('evaluation', '!=', null)->count()
                ];
                break;
        }

        // Generate PDF using appropriate template
        $pdf = Pdf::loadView("admins.hrStaff.reports.{$type}", $reportData);

        return $pdf->download("{$type}-report-" . now()->format('Y-m-d') . ".pdf");
    }

    /**
     * Get formatted notifications from database
     */
    private function getFormattedNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->take(20)->get();
        $formattedNotifications = [];

        foreach ($notifications as $notification) {
            $data = $notification->data;

            // Skip leave notifications that are no longer pending
            if ($this->isLeaveNotification($data) && !$this->isLeavePending($data)) {
                continue;
            }

            $formattedNotifications[] = array_merge($data, [
                'read_at' => $notification->read_at,
                'id' => $notification->id,
                'created_at' => $notification->created_at,
            ]);
        }

        return $formattedNotifications;
    }

    /**
     * Check if notification is a leave request notification
     */
    private function isLeaveNotification($notificationData)
    {
        return isset($notificationData['leave_form_id']);
    }

    /**
     * Check if leave form is still pending (not yet processed)
     */
    private function isLeavePending($notificationData)
    {
        if (!isset($notificationData['leave_form_id'])) {
            return true; // Not a leave notification, consider it valid
        }

        $leaveFormId = $notificationData['leave_form_id'];

        // Check if leave form exists and is still pending
        $leaveForm = LeaveForm::find($leaveFormId);

        // If leave form doesn't exist (deleted) or status is not Pending, filter it out
        if (!$leaveForm || $leaveForm->status !== 'Pending') {
            return false;
        }

        return true;
    }
}
