@extends('emails.layout')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
  </h2>

  <p style="margin:0 0 16px; line-height:1.6;">
    Thank you for taking the time to attend the interview for the position of 
    <strong>{{ $application->job->job_title }}</strong> at 
    <strong>{{ $application->job->company_name }}</strong>.
  </p>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
         style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
    <tr>
      <td style="padding:14px 16px;">
        <p style="margin:0; font-size:14px; color:#334155;">
          After careful evaluation, we are pleased to inform you that you have 
          <strong>Successfully Passed</strong> the interview stage and have been selected to proceed with the next step.
        </p>
      </td>
    </tr>
  </table>

    <p style="margin:0;">
        Our HR team will be coordinating your training schedule, and we will notify you of the specific date and time once the arrangements have been finalized. Please keep your lines open and check your email regularly for updates.
    </p>
@endsection
