<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveForm;

class DashboardChartController extends Controller
{
    public function index()
    {
        // Labels for months
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Get distinct company names from jobs table (sanitized)
        $companies = DB::table('jobs')
            ->distinct()
            ->pluck('company_name')
            ->filter(function($name) {
                // Ensure only valid company names (extra safety layer)
                return !empty($name) && is_string($name);
            });

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
            $counts = DB::table('jobs')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->where('company_name', $company)  // Parameter binding prevents SQL injection
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $jobData[$company] = $fillMonths($counts);
        }

        // ==============================
        // APPLICANTS (per company from applications table)
        // ==============================
        $applicantData = [];
        foreach ($companies as $company) {
            $counts = DB::table('applications')
                ->join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('users', 'applications.user_id', '=', 'users.id')
                ->selectRaw('MONTH(applications.created_at) as month, COUNT(*) as total')
                ->where('users.role', 'applicant')
                ->where('jobs.company_name', $company)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $applicantData[$company] = $fillMonths($counts);
        }

        // ==============================
        // EMPLOYEES (per company from applications table)
        // ==============================
        $employeeData = [];
        foreach ($companies as $company) {
            $counts = DB::table('applications')
                ->join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('users', 'applications.user_id', '=', 'users.id')
                ->selectRaw('MONTH(applications.created_at) as month, COUNT(*) as total')
                ->where('users.role', 'employee')
                ->where('jobs.company_name', $company)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $employeeData[$company] = $fillMonths($counts);
        }

        // ==============================
        // LEAVE FORMS (group by status)
        // ==============================
        $leaveCounts = LeaveForm::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Normalize leave data (support both string + numeric status codes, always return integers)
        $leaveData = [
            'pending'  => (int)($leaveCounts['Pending'] ?? $leaveCounts[0] ?? 0),
            'approved' => (int)($leaveCounts['Approved'] ?? $leaveCounts[1] ?? 0),
            'rejected' => (int)($leaveCounts['Declined'] ?? $leaveCounts[2] ?? 0),
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
        $stats = [
            'jobs'       => DB::table('jobs')->count(),
            'applicants' => DB::table('users')->where('role', 'applicant')->count(),
            'employees'  => DB::table('users')->where('role', 'employee')->count(),
        ];

        return view('admins.hrAdmin.dashboard', compact('chartData', 'stats', 'leaveData'));
    }
}
