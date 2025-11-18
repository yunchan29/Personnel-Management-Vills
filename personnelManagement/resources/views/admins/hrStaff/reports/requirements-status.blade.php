<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requirements Status Report</title>
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
        .check-yes {
            color: #28a745;
            font-weight: bold;
        }
        .check-no {
            color: #dc3545;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #28a745;
            transition: width 0.3s;
        }
        .progress-fill-partial {
            background: #ffc107;
        }
        .progress-fill-low {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Requirements Status Report</h1>
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
                <span class="stat-label">Total Applications</span>
                <span class="stat-value">{{ $stats['total_applications'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">With Interview</span>
                <span class="stat-value">{{ $stats['with_interview'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">With Training</span>
                <span class="stat-value">{{ $stats['with_training'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">With Evaluation</span>
                <span class="stat-value">{{ $stats['with_evaluation'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 18%;">Applicant Name</th>
                <th style="width: 16%;">Position</th>
                <th style="width: 14%;">Company</th>
                <th style="width: 8%;">Interview</th>
                <th style="width: 8%;">Training</th>
                <th style="width: 8%;">Evaluation</th>
                <th style="width: 12%;">Current Status</th>
                <th style="width: 12%;">Progress</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $application)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $application->user->first_name }} {{ $application->user->last_name }}</td>
                <td>{{ $application->job->job_title }}</td>
                <td>{{ $application->job->company_name }}</td>
                <td>
                    @if($application->interview)
                        <span class="check-yes">✓ Yes</span>
                    @else
                        <span class="check-no">✗ No</span>
                    @endif
                </td>
                <td>
                    @if($application->trainingSchedule)
                        <span class="check-yes">✓ Yes</span>
                    @else
                        <span class="check-no">✗ No</span>
                    @endif
                </td>
                <td>
                    @if($application->evaluation)
                        <span class="check-yes">✓ Yes</span>
                    @else
                        <span class="check-no">✗ No</span>
                    @endif
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $application->status->value)) }}</td>
                <td>
                    @php
                        $completed = 0;
                        if($application->interview) $completed++;
                        if($application->trainingSchedule) $completed++;
                        if($application->evaluation) $completed++;
                        $percentage = ($completed / 3) * 100;
                        $progressClass = $percentage >= 67 ? 'progress-fill' :
                                       ($percentage >= 34 ? 'progress-fill-partial' : 'progress-fill-low');
                    @endphp
                    <div class="progress-bar">
                        <div class="{{ $progressClass }}" style="width: {{ $percentage }}%;"></div>
                    </div>
                    {{ $completed }}/3
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                        No application data found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($applications->count() > 0)
    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #BD6F22;">
        <p style="margin: 0; font-size: 11px; color: #555;">
            <strong>Progress Legend:</strong><br>
            • <span class="check-yes">✓ Yes</span> - Requirement completed<br>
            • <span class="check-no">✗ No</span> - Requirement pending<br>
            • <strong>Progress:</strong> Shows completion of Interview + Training + Evaluation (total of 3 requirements)
        </p>
    </div>
    @endif

    <div class="footer">
        <p><strong>Personnel Management System - Vills</strong></p>
        <p>This is a system-generated report. For questions, contact HR Administration.</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
