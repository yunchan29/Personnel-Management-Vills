<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Completion Report</title>
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
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        .badge-in-progress {
            background: #cfe2ff;
            color: #084298;
        }
        .badge-scheduled {
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Training Completion Report</h1>
        <p><strong>Generated:</strong> {{ $generated_date }}</p>
        <p>Personnel Management System - Vills</p>
    </div>

    @if(!empty($filters) && (isset($filters['company']) || isset($filters['position']) || isset($filters['start_date'])))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['company']) && $filters['company'])
            <div class="filter-item"><span class="filter-label">Company:</span> {{ $filters['company'] }}</div>
        @endif
        @if(isset($filters['position']) && $filters['position'])
            <div class="filter-item"><span class="filter-label">Position:</span> {{ $filters['position'] }}</div>
        @endif
        @if(isset($filters['start_date']) && $filters['start_date'])
            <div class="filter-item"><span class="filter-label">From:</span> {{ $filters['start_date'] }}</div>
        @endif
        @if(isset($filters['end_date']) && $filters['end_date'])
            <div class="filter-item"><span class="filter-label">To:</span> {{ $filters['end_date'] }}</div>
        @endif
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-cell">
                <span class="stat-label">Total Trainings</span>
                <span class="stat-value">{{ $stats['total_trained'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Completed</span>
                <span class="stat-value">{{ $stats['completed'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">In Progress</span>
                <span class="stat-value">{{ $stats['in_progress'] ?? 0 }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Scheduled</span>
                <span class="stat-value">{{ $stats['scheduled'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Trainee Name</th>
                <th style="width: 18%;">Position</th>
                <th style="width: 15%;">Company</th>
                <th style="width: 10%;">Start Date</th>
                <th style="width: 10%;">End Date</th>
                <th style="width: 8%;">Duration</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 6%;">Location</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $application)
                @if($application->trainingSchedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $application->user->first_name }} {{ $application->user->last_name }}</td>
                    <td>{{ $application->job->job_title }}</td>
                    <td>{{ $application->job->company_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($application->trainingSchedule->start_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($application->trainingSchedule->end_date)->format('M d, Y') }}</td>
                    <td>
                        @php
                            $start = \Carbon\Carbon::parse($application->trainingSchedule->start_date);
                            $end = \Carbon\Carbon::parse($application->trainingSchedule->end_date);
                            $duration = $start->diffInDays($end);
                        @endphp
                        {{ $duration }} {{ $duration == 1 ? 'day' : 'days' }}
                    </td>
                    <td>
                        @php
                            $status = $application->trainingSchedule->status;
                            $badgeClass = $status === 'completed' ? 'badge-completed' :
                                         ($status === 'in_progress' ? 'badge-in-progress' : 'badge-scheduled');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ strtoupper($status) }}</span>
                    </td>
                    <td>{{ $application->trainingSchedule->location ?? 'TBD' }}</td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                        No training data found for the selected filters.
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
