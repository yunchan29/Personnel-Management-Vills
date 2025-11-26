<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #374151; margin: 30px; line-height: 1.6; }
        h2 { font-size: 22px; font-weight: bold; color: #1e40af; margin-bottom: 8px; }

        .header { border-bottom: 3px solid #1e40af; padding-bottom: 10px; margin-bottom: 20px; }
        .meta { font-size: 13px; color: #4B5563; margin: 4px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #F3F4F6; color: #374151; font-size: 13px; font-weight: 600; padding: 10px; border: 1px solid #D1D5DB; text-align: left; }
        td { padding: 10px; border: 1px solid #E5E7EB; font-size: 12px; vertical-align: middle; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h2>Employee Report</h2>

        <p class="meta">Range: <strong>{{ ucfirst($range) }}</strong></p>
        <p class="meta">Company: 
            <strong>{{ $filterCompany === 'all' ? 'All Companies' : $filterCompany }}</strong>
        </p>
        <p class="meta">Date & Time Generated: 
            <strong>{{ now()->format('F d, Y h:i A') }}</strong>
        </p>

        @if($range === 'custom')
            <p class="meta">
                Custom Range:
                <strong>{{ \Carbon\Carbon::parse($start)->format('F d, Y') }}</strong>
                —
                <strong>{{ \Carbon\Carbon::parse($end)->format('F d, Y') }}</strong>
            </p>
        @endif
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Position</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($employees as $emp)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $emp->user->full_name ?? '' }}</td>
                    <td>{{ $emp->user->email ?? '' }}</td>

                    <td>{{ $emp->company_name ?? '' }}</td>
                    <td>{{ $emp->position_title ?? '' }}</td>

                    <td>
                        {{ $emp->start_date ? \Carbon\Carbon::parse($emp->start_date)->format('F d, Y') : '—' }}
                    </td>

                    <td>
                        {{ $emp->end_date
                            ? \Carbon\Carbon::parse($emp->end_date)->format('F d, Y')
                            : 'Present'
                        }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6B7280; padding: 15px;">
                        No employees found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
