@extends('emails.layout')
@section('title', 'Leave Update')

@section('content')
    @php
        // Split "MM/DD/YYYY - MM/DD/YYYY"
        $dates = explode(' - ', $leaveform->date_range);

        $start = \Carbon\Carbon::parse($dates[0])->format('F j, Y');
        $end   = isset($dates[1])
                    ? \Carbon\Carbon::parse($dates[1])->format('F j, Y')
                    : null;
    @endphp

    <h2 style="margin:0 0 12px; font-size:22px; color:#b91c1c;">
        Leave Application Declined
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $leaveform->user->first_name ?? $leaveform->user->name }}
             {{ $leaveform->user->last_name ?? '' }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We regret to inform you that your leave application for the period 
        <strong>{{ $start }}</strong> to <strong>{{ $end }}</strong>
        has been <strong>declined</strong>.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        <strong>General Reason:</strong><br>
        Due to current staffing requirements and operational needs, we are unable to approve your leave request at this time.
    </p>

    @if (!empty($leaveform->about))
        <p style="margin:0 0 16px; line-height:1.6;">
            <strong>Additional Notes from HR:</strong><br>
            {{ $leaveform->about }}
        </p>
    @endif

    <p style="margin:0; line-height:1.6;">
        If you have any questions or would like to discuss this matter further, please contact the HR department.
    </p>
@endsection
