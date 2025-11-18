<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* Page setup with proper print margins */
        @page {
            margin: 20px;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            orphans: 3;
            widows: 3;
        }

        .page-wrapper {
            padding: 0; /* margin handled by body padding */
        }

        /* Header section - keep together on page */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 4px solid #BD6F22;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .header h1 {
            color: #BD6F22;
            font-size: 22px;
            margin-bottom: 5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            color: #666;
            font-size: 9px;
        }

        /* Filter info - keep together */
        .filter-info {
            background-color: #F8F8F8;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #DDD;
            border-left: 5px solid #BD6F22;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .filter-info h3 {
            font-size: 11px;
            color: #BD6F22;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .filter-info p {
            font-size: 9px;
            margin: 3px 0;
            color: #444;
        }

        /* Sections - prevent breaking inside */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        /* Section titles - keep with content */
        .section-title {
            background: #BD6F22;
            color: white;
            padding: 9px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 2px;
            letter-spacing: 0.3px;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* Stats grid - prevent breaking */
        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .stats-grid td {
            width: 33.33%;
            padding: 12px 8px;
            text-align: center;
            border: 2px solid #BD6F22;
            background-color: #FFF9F0;
            word-wrap: break-word;
        }

        .stat-value {
            font-size: 26px;
            font-weight: bold;
            color: #BD6F22;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 9px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables - with repeating headers and page break control */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #CCC;
            page-break-inside: auto;
        }

        table thead {
            display: table-header-group;
        }

        table tbody {
            display: table-row-group;
        }

        table th {
            background-color: #BD6F22;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #A55E1A;
            letter-spacing: 0.3px;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        table td {
            padding: 7px 10px;
            border: 1px solid #DDD;
            font-size: 9px;
            background-color: white;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table tr {
            page-break-inside: avoid;
        }

        table tr:nth-child(even) td {
            background-color: #FAFAFA;
        }

        table tbody tr:last-child td {
            border-bottom: 2px solid #BD6F22;
        }

        /* Metric boxes - prevent breaking */
        .metric-box {
            background-color: #FFFAF0;
            border: 2px solid #E0E0E0;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 5px solid #BD6F22;
            border-radius: 2px;
            page-break-inside: avoid;
        }

        .metric-box .metric-title {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-box .metric-value {
            font-size: 22px;
            font-weight: bold;
            color: #BD6F22;
        }

        /* Two column layout */
        .two-column {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .column {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 3px solid #BD6F22;
            text-align: center;
            font-size: 8px;
            color: #999;
            page-break-inside: avoid;
        }

        /* Utility classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Bold rows */
        .bold-row {
            background-color: #F0F0F0 !important;
            font-weight: bold !important;
        }

        .bold-row td {
            font-weight: bold;
            border-top: 2px solid #BD6F22;
            background-color: #F5F5F5 !important;
        }

        /* Print-specific optimizations */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            @page {
                margin: 2cm 1.5cm !important;
                size: A4 portrait;
            }
    
            .section {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            table thead {
                display: table-header-group;
            }

            table tfoot {
                display: table-footer-group;
            }

            .header, .filter-info, .metric-box, .stats-grid {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <p class="subtitle">Generated on {{ $generated_date }} at {{ $generated_time }}</p>
    </div>

    <!-- Filter Information -->
    <div class="filter-info">
        <h3>Report Filters</h3>
        <p><strong>Company:</strong> {{ $filter_company }}</p>
        <p><strong>Date Range:</strong>
            @if($filter_start_date === 'N/A' && $filter_end_date === 'N/A')
                All Dates
            @else
                {{ $filter_start_date }} to {{ $filter_end_date }}
            @endif
        </p>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <span class="stat-value">{{ $stats['jobs'] }}</span>
                    <span class="stat-label">Total Jobs Posted</span>
                </td>
                <td>
                    <span class="stat-value">{{ $stats['applicants'] }}</span>
                    <span class="stat-label">Total Applicants</span>
                </td>
                <td>
                    <span class="stat-value">{{ $stats['employees'] }}</span>
                    <span class="stat-label">Total Employees</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Key Metrics -->
    <div class="section">
        <div class="section-title">Key Performance Indicators</div>
        <table class="two-column">
            <tr>
                <td class="column">
                    <div class="metric-box">
                        <div class="metric-title">Hiring Efficiency</div>
                        <div class="metric-value">{{ $hiringEfficiency }}%</div>
                    </div>
                    <div class="metric-box">
                        <div class="metric-title">Training Pass Rate</div>
                        <div class="metric-value">{{ $trainingPassRate }}%</div>
                    </div>
                </td>
                <td class="column">
                    <div class="metric-box">
                        <div class="metric-title">Time-to-Hire (Average Days)</div>
                        <div class="metric-value">{{ $timeToHire }}</div>
                    </div>
                    <div class="metric-box">
                        <div class="metric-title">Leave Approval Rate</div>
                        <div class="metric-value">{{ $leaveApprovalRate }}%</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Application Pipeline -->
    <div class="section">
        <div class="section-title">Application Pipeline Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalApplications = array_sum($pipelineFunnel);
                @endphp
                @foreach($pipelineFunnel as $status => $count)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                    <td class="text-center">{{ $count }}</td>
                    <td class="text-right">{{ $totalApplications > 0 ? round(($count / $totalApplications) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
                <tr class="bold-row">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $totalApplications }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Interview Statistics -->
    <div class="section">
        <div class="section-title">Interview Statistics</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Interviews</td>
                    <td class="text-center">{{ $interviewStats['total'] }}</td>
                </tr>
                <tr>
                    <td>Scheduled</td>
                    <td class="text-center">{{ $interviewStats['scheduled'] }}</td>
                </tr>
                <tr>
                    <td>Rescheduled</td>
                    <td class="text-center">{{ $interviewStats['rescheduled'] }}</td>
                </tr>
                <tr>
                    <td>Completed</td>
                    <td class="text-center">{{ $interviewStats['completed'] }}</td>
                </tr>
                <tr>
                    <td>Cancelled</td>
                    <td class="text-center">{{ $interviewStats['cancelled'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Training & Evaluation -->
    <div class="section">
        <div class="section-title">Training & Evaluation Metrics</div>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-center">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Training Sessions</td>
                    <td class="text-center">{{ $trainingStats['total_trainings'] }}</td>
                </tr>
                <tr>
                    <td>Scheduled</td>
                    <td class="text-center">{{ $trainingStats['scheduled'] }}</td>
                </tr>
                <tr>
                    <td>In Progress</td>
                    <td class="text-center">{{ $trainingStats['in_progress'] }}</td>
                </tr>
                <tr>
                    <td>Completed</td>
                    <td class="text-center">{{ $trainingStats['completed'] }}</td>
                </tr>
                <tr class="bold-row">
                    <td>Total Evaluations</td>
                    <td class="text-center">{{ $trainingStats['total_evaluations'] }}</td>
                </tr>
                <tr>
                    <td>Passed</td>
                    <td class="text-center" style="color: green;">{{ $trainingStats['passed'] }}</td>
                </tr>
                <tr>
                    <td>Failed</td>
                    <td class="text-center" style="color: red;">{{ $trainingStats['failed'] }}</td>
                </tr>
                <tr>
                    <td>Average Score</td>
                    <td class="text-center">{{ $trainingStats['avg_score'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Job Metrics -->
    <div class="section">
        <div class="section-title">Job Metrics</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Active Jobs</td>
                    <td class="text-center">{{ $jobMetrics['active'] }}</td>
                </tr>
                <tr>
                    <td>Filled Jobs</td>
                    <td class="text-center">{{ $jobMetrics['filled'] }}</td>
                </tr>
                <tr>
                    <td>Expired Jobs</td>
                    <td class="text-center">{{ $jobMetrics['expired'] }}</td>
                </tr>
                <tr class="bold-row">
                    <td>Fill Rate</td>
                    <td class="text-center">{{ $jobMetrics['fill_rate'] }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Top Jobs -->
    <div class="section">
        <div class="section-title">Top 10 Jobs by Applications</div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th class="text-center">Applications</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topJobs as $index => $job)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $job->job_title ?? 'N/A' }}</td>
                    <td>{{ $job->company_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $job->applications_count ?? 0 }}</td>
                </tr>
                @endforeach
                @if(count($topJobs) == 0)
                <tr>
                    <td colspan="4" class="text-center" style="color: #999;">No job data available for the selected filters</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Leave Management -->
    <div class="section">
        <div class="section-title">Leave Management Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pending</td>
                    <td class="text-center">{{ $leaveData['pending'] }}</td>
                    <td class="text-right">{{ $totalLeaves > 0 ? round(($leaveData['pending'] / $totalLeaves) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Approved</td>
                    <td class="text-center">{{ $leaveData['approved'] }}</td>
                    <td class="text-right">{{ $totalLeaves > 0 ? round(($leaveData['approved'] / $totalLeaves) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Rejected</td>
                    <td class="text-center">{{ $leaveData['rejected'] }}</td>
                    <td class="text-right">{{ $totalLeaves > 0 ? round(($leaveData['rejected'] / $totalLeaves) * 100, 1) : 0 }}%</td>
                </tr>
                <tr class="bold-row">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $totalLeaves }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the HR Management System.</p>
        <p>&copy; {{ date('Y') }} Personnel Management System. All rights reserved.</p>
    </div>
</div>
</body>
</html>
