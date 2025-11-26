<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #374151; margin: 20px; line-height: 1.4; }
        h2 { font-size: 20px; font-weight: bold; color: #1e40af; margin-bottom: 8px; }

        .header { border-bottom: 3px solid #1e40af; padding-bottom: 8px; margin-bottom: 15px; }
        .meta { font-size: 11px; color: #4B5563; margin: 3px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        th { background: #F3F4F6; color: #374151; font-size: 10px; font-weight: 600; padding: 6px 4px; border: 1px solid #D1D5DB; text-align: left; }
        td { padding: 6px 4px; border: 1px solid #E5E7EB; font-size: 9px; vertical-align: top; word-wrap: break-word; }

        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 4%; text-align: center; } /* # */
        th:nth-child(2), td:nth-child(2) { width: 14%; } /* Employee Name */
        th:nth-child(3), td:nth-child(3) { width: 20%; font-size: 8px; } /* Email */
        th:nth-child(4), td:nth-child(4) { width: 16%; } /* Company */
        th:nth-child(5), td:nth-child(5) { width: 16%; } /* Position */
        th:nth-child(6), td:nth-child(6) { width: 15%; } /* Start Date */
        th:nth-child(7), td:nth-child(7) { width: 15%; } /* End Date */
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

                    <td>{{ $emp->job->company_name ?? '' }}</td>
                    <td>{{ $emp->job->job_title ?? '' }}</td>

                    <td>
                        {{ $emp->contract_start ? \Carbon\Carbon::parse($emp->contract_start)->format('M d, Y') : '—' }}
                    </td>

                    <td>
                        {{ $emp->contract_end
                            ? \Carbon\Carbon::parse($emp->contract_end)->format('M d, Y')
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
