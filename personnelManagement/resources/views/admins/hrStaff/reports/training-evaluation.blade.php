<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Evaluation Report</title>
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
            width: 25%;
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
        .badge-passed {
            background: #d4edda;
            color: #155724;
        }
        .badge-failed {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .score-high {
            color: #28a745;
            font-weight: bold;
        }
        .score-medium {
            color: #ffc107;
            font-weight: bold;
        }
        .score-low {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Training Evaluation Report</h1>
        <p><strong>Generated:</strong> {{ $generated_date }}</p>
        <p>Personnel Management System - Vills</p>
    </div>

    @if(!empty($filters) && (isset($filters['company']) || isset($filters['position']) || isset($filters['year']) || isset($filters['month']) || isset($filters['evaluation_status'])))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['company']) && $filters['company'])
            <div class="filter-item"><span class="filter-label">Company:</span> {{ $filters['company'] }}</div>
        @endif
        @if(isset($filters['position']) && $filters['position'])
            <div class="filter-item"><span class="filter-label">Position:</span> {{ $filters['position'] }}</div>
        @endif
        @if(isset($filters['evaluation_status']) && $filters['evaluation_status'])
            @php
                $statusLabel = $filters['evaluation_status'] === 'passed' ? 'Passed (≥70)' :
                              ($filters['evaluation_status'] === 'failed' ? 'Failed (<70)' : 'Pending Evaluation');
            @endphp
            <div class="filter-item"><span class="filter-label">Evaluation Status:</span> {{ $statusLabel }}</div>
        @else
            <div class="filter-item"><span class="filter-label">Evaluation Status:</span> All (Passed & Failed)</div>
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
        @if(isset($filters['min_score']) && $filters['min_score'])
            <div class="filter-item"><span class="filter-label">Min Score:</span> {{ $filters['min_score'] }}%</div>
        @endif
        @if(isset($filters['max_score']) && $filters['max_score'])
            <div class="filter-item"><span class="filter-label">Max Score:</span> {{ $filters['max_score'] }}%</div>
        @endif
    </div>
    @else
    <div class="filters">
        <h3>Report Scope:</h3>
        <div class="filter-item"><span class="filter-label">Evaluation Status:</span> All Applicants (Passed & Failed)</div>
        <div class="filter-item"><span class="filter-label">Period:</span> All Time</div>
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-cell">
                <span class="stat-label">Total Evaluations</span>
                <span class="stat-value">{{ $stats['total_evaluations'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Passed</span>
                <span class="stat-value">{{ $stats['passed'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Failed</span>
                <span class="stat-value">{{ $stats['failed'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Average Score</span>
                <span class="stat-value">{{ number_format($stats['avg_score'] ?? 0, 1) }}%</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Applicant Name</th>
                <th style="width: 20%;">Position</th>
                <th style="width: 15%;">Company</th>
                <th style="width: 12%;">Evaluation Date</th>
                <th style="width: 10%;">Score</th>
                <th style="width: 10%;">Result</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $application)
                @if($application->evaluation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $application->user->first_name }} {{ $application->user->last_name }}</td>
                    <td>{{ $application->job->job_title }}</td>
                    <td>{{ $application->job->company_name }}</td>
                    <td>{{ $application->evaluation->created_at->format('M d, Y') }}</td>
                    <td>
                        @php
                            $score = $application->evaluation->total_score;
                            $scoreClass = $score >= 85 ? 'score-high' : ($score >= 70 ? 'score-medium' : 'score-low');
                        @endphp
                        <span class="{{ $scoreClass }}">{{ number_format($score, 1) }}%</span>
                    </td>
                    <td>
                        @if($application->evaluation->result === 'Passed')
                            <span class="badge badge-passed">PASSED</span>
                        @else
                            <span class="badge badge-failed">FAILED</span>
                        @endif
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $application->status->value)) }}</td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                        No evaluation data found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Personnel Management System - Vills</strong></p>
        <p>This is a system-generated report. For questions, contact HR Administration.</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
