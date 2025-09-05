<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants Report</title>
    <style>
        /* Page layout */
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #374151; margin: 30px; line-height: 1.6; }
        h2 { font-size: 22px; font-weight: bold; color: #BD6F22; margin-bottom: 8px; }
        .header { border-bottom: 3px solid #BD6F22; padding-bottom: 10px; margin-bottom: 20px; }
        .meta { font-size: 13px; color: #4B5563; margin: 4px 0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #F3F4F6; color: #374151; font-size: 13px; font-weight: 600; padding: 10px; border: 1px solid #D1D5DB; text-align: left; }
        td { padding: 10px; border: 1px solid #E5E7EB; font-size: 12px; vertical-align: middle; }

        /* Status badges */
        .status-approved { background: #DCFCE7; color: #166534; font-weight: 600; text-align: center; padding: 6px; border-radius: 6px; display: inline-block; }
        .status-declined { background: #FEE2E2; color: #991B1B; font-weight: 600; text-align: center; padding: 6px; border-radius: 6px; display: inline-block; }
        .status-default { background: #DBEAFE; color: #1E3A8A; font-weight: 600; text-align: center; padding: 6px; border-radius: 6px; display: inline-block; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>Applicants Report</h2>
        <p class="meta">Status: <strong>{{ ucfirst($status) }}</strong></p>
        <p class="meta">Range: <strong>{{ ucfirst($range) }}</strong></p>
        <p class="meta">Date & Time Generated: <strong>{{ now()->format('F d, Y h:i A') }}</strong></p>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Job Title</th>
                <th>Company</th>
                <th>Status</th>
                <th>Applied On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $app)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $app->user->full_name ?? '' }}</td>
                    <td>{{ $app->user->email ?? '' }}</td>
                    <td>{{ $app->job->job_title ?? '' }}</td>
                    <td>{{ $app->job->company_name ?? '' }}</td>
                    <td>
                        @if ($app->status === 'approved')
                            <span class="status-approved">Approved</span>
                        @elseif ($app->status === 'declined' || $app->status === 'disapproved')
                            <span class="status-declined">Declined</span>
                        @else
                            <span class="status-default">{{ ucfirst($app->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $app->created_at->format('F d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6B7280; padding: 15px;">
                        No applicants found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
