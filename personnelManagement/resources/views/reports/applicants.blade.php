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

        /* Status badges (PDF safe colors; classes used dynamically) */
        .bg-gray-100 { background: #F3F4F6; }
        .bg-green-100 { background: #DCFCE7; }
        .bg-red-100 { background: #FEE2E2; }
        .bg-yellow-100 { background: #FEF9C3; }
        .bg-blue-100 { background: #DBEAFE; }
        .bg-purple-100 { background: #EDE9FE; }

        .text-gray-800 { color: #374151; }
        .text-green-800 { color: #166534; }
        .text-red-800 { color: #991B1B; }
        .text-yellow-800 { color: #B45309; }
        .text-blue-800 { color: #1E3A8A; }
        .text-purple-800 { color: #5B21B6; }

        .badge {
            font-weight: 600;
            text-align: center;
            padding: 6px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>Applicants Report</h2>
        <p class="meta">Status: <strong>{{ ucfirst($status) }}</strong></p>
        <p class="meta">Range: <strong>{{ ucfirst($range) }}</strong></p>
        <p class="meta">Date & Time Generated: <strong>{{ now()->format('F d, Y h:i A') }}</strong></p>
        <p class="meta"> Company: <strong>{{ $filterCompany === 'all' ? 'All Companies' : $filterCompany }} </strong> </p>
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

                    <!-- FIXED STATUS SECTION -->
                    <td>
                        <span class="badge {{ $app->status->badgeClass() }}">
                            {{ $app->status->label() }}
                        </span>
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
