@extends('emails.layout')

@section('title', 'Interview Result')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Interview Result – {{ $application->job->job_title }}
  </h2>

  <p style="margin:0 0 16px; line-height:1.6;">
    Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
  </p>

  <p style="margin:0 0 16px; line-height:1.6;">
    Thank you for taking the time to attend the interview for the position of 
    <strong>{{ $application->job->job_title }}</strong> at 
    <strong>{{ $application->job->company_name }}</strong>.
  </p>

  <!-- Highlight Box -->
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
    style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
    <tr>
      <td style="padding:14px 16px;">
        <p style="margin:0; font-size:14px; color:#9a3412;">
          After careful consideration, we’re sorry to inform you that you were 
          <strong>not selected to proceed</strong> at this time.
        </p>
      </td>
    </tr>
  </table>

  <p style="margin:0 0 16px; line-height:1.6;">
    We truly appreciate the effort you put into your application and interview, and we encourage you to apply again for future opportunities that match your skills and qualifications.
  </p>

  <p style="margin:0; line-height:1.6;">
    We wish you success in your career endeavors.
  </p>
@endsection
