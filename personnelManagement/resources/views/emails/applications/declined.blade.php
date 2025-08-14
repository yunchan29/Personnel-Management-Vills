@extends('emails.layout')

@section('title', 'Application Declined')

@section('content')
    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Application Update – {{ $application->job->job_title }}
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Thank you for applying for the position of <strong>{{ $application->job->job_title }}</strong> at <strong>{{ $application->job->company_name }}</strong>.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
        style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
        <tr>
        <td style="padding:14px 16px;">
            <p style="margin:0; font-size:14px; color:#9a3412;">
                After careful review of your application, we regret to inform you that you were not shortlisted for the next stage of our hiring process.
            </p>
        </td>
        </tr>
    </table>

    <p style="margin:0; line-height:1.6;">
         We appreciate your interest and encourage you to apply for future openings that match your qualifications.
    </p>

@endsection

