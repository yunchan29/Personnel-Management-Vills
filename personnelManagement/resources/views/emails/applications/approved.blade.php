@extends('emails.layout')

@section('title', 'Application Approved')

@section('content')
<h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Application Update – {{ $application->job->job_title }}
</h2>

  <p style="margin:0 0 16px; line-height:1.6;">
    Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
  </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We are pleased to inform you that, upon reviewing your 201 file, you have successfully passed our 
        <strong>Initial Screening</strong> for the position of 
        <strong>{{ $application->job->job_title }}</strong>.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
        style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
        <tr>
        <td style="padding:14px 16px;">
            <p style="margin:0; font-size:14px; color:#9a3412;">
                Our HR team will be sending you the interview date and details once the schedule is confirmed. Please ensure that your contact details are up-to-date and keep an eye on your email for updates.
            </p>
        </td>
        </tr>
    </table>

    <p style="margin:0 line-height:1.6;">
        For any inquiries, feel free to reach us at <a href="mailto:villsmanpower@gmail.com">villsmanpower@gmail.com</a>.
    </p>
@endsection
