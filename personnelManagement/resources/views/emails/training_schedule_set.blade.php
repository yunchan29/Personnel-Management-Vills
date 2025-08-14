@extends('emails.layout')
@section('title', 'Training Schedule Set')

@section('content')
    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Training Schedule – {{ $application->job->job_title }}
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Congratulations once again on passing the interview for the position of <strong>{{ $application->job->job_title }}</strong> at <strong>{{ $application->job->company_name }}</strong>.
    </p>


        <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
        style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
        <tr>
        <td style="padding:14px 16px;">
            <p style="margin:0; font-size:14px; color:#9a3412;">
                We are pleased to inform you that your training schedule has been finalized as follows:
            </p>
            <ul>
                <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('F j, Y') }}</li>
                <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($schedule->end_date)->format('F j, Y') }}</li>
            </ul>
        </td>
        </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        Please be guided accordingly and ensure to attend the training as scheduled. 
        Completion of the training is required before proceeding to the next step of the hiring process. 
        After the training, an evaluation will be conducted to determine your eligibility to proceed with your employment contract.
    </p>

    <p style="margin:0; line-height:1.6;">
      We look forward to having you onboard and beginning this exciting journey together.
    </p>
@endsection