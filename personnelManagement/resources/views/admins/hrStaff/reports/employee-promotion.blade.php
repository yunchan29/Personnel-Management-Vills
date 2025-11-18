<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Promotion Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #BD6F22;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #BD6F22;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .stat-row {
            display: table-row;
        }
        .stat-cell {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            text-align: center;
        }
        .stat-label {
            display: block;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            display: block;
            font-size: 20px;
            font-weight: bold;
            color: #BD6F22;
        }
        .filters {
            background: #f0f0f0;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            color: #555;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #BD6F22;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #BD6F22;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #BD6F22;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-hired {
            background: #d4edda;
            color: #155724;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-passed {
            background: #cfe2ff;
            color: #084298;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee Promotion Report</h1>
        <p><strong>Generated:</strong> {{ $generated_date }}</p>
        <p>Personnel Management System - Vills</p>
    </div>

    @if(!empty($filters) && (isset($filters['company']) || isset($filters['position']) || isset($filters['year']) || isset($filters['month'])))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['company']) && $filters['company'])
            <div class="filter-item"><span class="filter-label">Company:</span> {{ $filters['company'] }}</div>
        @endif
        @if(isset($filters['position']) && $filters['position'])
            <div class="filter-item"><span class="filter-label">Position:</span> {{ $filters['position'] }}</div>
        @endif
        @if(isset($filters['year']) && $filters['year'])
            <div class="filter-item"><span class="filter-label">Year:</span> {{ $filters['year'] }}</div>
        @endif
        @if(isset($filters['month']) && $filters['month'])
            @php
                $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                $monthName = $monthNames[$filters['month']] ?? $filters['month'];
            @endphp
            <div class="filter-item"><span class="filter-label">Month:</span> {{ $monthName }}</div>
        @endif
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-cell">
                <span class="stat-label">Total Promoted (Hired)</span>
                <span class="stat-value">{{ $stats['total_promoted'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Pending Promotion</span>
                <span class="stat-value">{{ $stats['pending_promotion'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    @php
        $promotedApplicants = $applications->filter(function($app) {
            return $app->status->value === 'hired';
        });
        $pendingApplicants = $applications->filter(function($app) {
            return in_array($app->status->value, ['trained', 'passed']);
        });
    @endphp

    @if($promotedApplicants->count() > 0)
    <div class="section-title">Successfully Promoted Employees</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Employee Name</th>
                <th style="width: 20%;">Position</th>
                <th style="width: 18%;">Company</th>
                <th style="width: 12%;">Application Date</th>
                <th style="width: 12%;">Promotion Date</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotedApplicants as $index => $application)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $application->user->first_name }} {{ $application->user->last_name }}</td>
                <td>{{ $application->job->job_title }}</td>
                <td>{{ $application->job->company_name }}</td>
                <td>{{ $application->created_at->format('M d, Y') }}</td>
                <td>{{ $application->updated_at->format('M d, Y') }}</td>
                <td><span class="badge badge-hired">HIRED</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($pendingApplicants->count() > 0)
    <div class="section-title">Candidates Pending Promotion</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Candidate Name</th>
                <th style="width: 20%;">Position</th>
                <th style="width: 18%;">Company</th>
                <th style="width: 12%;">Evaluation Score</th>
                <th style="width: 12%;">Current Status</th>
                <th style="width: 8%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingApplicants as $index => $application)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $application->user->first_name }} {{ $application->user->last_name }}</td>
                <td>{{ $application->job->job_title }}</td>
                <td>{{ $application->job->company_name }}</td>
                <td>
                    @if($application->evaluation)
                        {{ number_format($application->evaluation->total_score, 1) }}%
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <span class="badge badge-pending">{{ strtoupper(str_replace('_', ' ', $application->status->value)) }}</span>
                </td>
                <td>
                    @if($application->evaluation && $application->evaluation->result === 'passed')
                        <span class="badge badge-passed">READY</span>
                    @else
                        <span class="badge badge-pending">REVIEW</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($promotedApplicants->count() == 0 && $pendingApplicants->count() == 0)
    <div style="text-align: center; padding: 40px; color: #999;">
        <p>No promotion data found for the selected filters.</p>
    </div>
    @endif

    <div class="footer">
        <p><strong>Personnel Management System - Vills</strong></p>
        <p>This is a system-generated report. For questions, contact HR Administration.</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
