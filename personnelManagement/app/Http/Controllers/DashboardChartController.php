<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardChartController extends Controller
{
    public function index()
    {
        // Labels for months
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Get distinct company names from jobs table
        $companies = DB::table('jobs')
            ->distinct()
            ->pluck('company_name');

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
            $counts = DB::table('jobs')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->where('company_name', $company)
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

        return view('hrAdmin.dashboard', compact('chartData', 'stats'));
    }
}
