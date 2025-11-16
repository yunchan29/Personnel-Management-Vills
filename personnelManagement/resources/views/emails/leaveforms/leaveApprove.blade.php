@extends('emails.layout')
@section('title', 'Leave Update')

@section('content')
    @php
        // Your stored format: "MM/DD/YYYY - MM/DD/YYYY"
        $dates = explode(' - ', $leaveform->date_range);

        $start = \Carbon\Carbon::parse($dates[0])->format('F j, Y');
        $end   = isset($dates[1])
                    ? \Carbon\Carbon::parse($dates[1])->format('F j, Y')
                    : null;
    @endphp

    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Leave Application Approved
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $leaveform->user->first_name ?? $leaveform->user->name }}
             {{ $leaveform->user->last_name ?? '' }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We are pleased to inform you that your leave application has been approved.
        Your leave is scheduled from 
        <strong>{{ $start }}</strong> to 
        <strong>{{ $end }}</strong>.
    </p>

    <p style="margin:0; line-height:1.6;">
        Please ensure that all necessary arrangements are made prior to your leave period. 
        If you have any questions or require further assistance, feel free to contact the HR department.
    </p>
@endsection
