@extends('emails.layout')

@section('title', 'Interview Reschedule')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Interview Reschedule – {{ $interview->application->job->job_title }}
  </h2>

  <p style="margin:0 0 16px; line-height:1.6;">
    Dear {{ $interview->applicant->first_name }} {{ $interview->applicant->last_name }},
  </p>

  <p style="margin:0 0 16px; line-height:1.6;">
    We hope you’re doing well. We would like to inform you that your interview has been <strong>rescheduled</strong> due to internal adjustments in our team’s availability.
  </p>

  <!-- Highlight Box -->
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
      style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
      <tr>
        <td style="padding:14px 16px;">
              <h3 style="margin:20px 0 12px; font-size:18px; color:#0f172a;"><strong>New Interview Schedule:</strong></h3>

              <p style="margin:0 0 8px; font-size:16px;">📅 Date: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('F j, Y') }}</strong></p>  
              <p style="margin:0 0 16px; font-size:16px;">🕙 Time: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('h:i A') }}</strong></p>  
        </td>
      </tr>
  </table>

  <p style="margin:0 0 16px; line-height:1.6;">
    We appreciate your flexibility and understanding regarding this change. If you have any concerns about the new schedule, please don’t hesitate to contact us.
  </p>

  <p style="margin:0; line-height:1.6;">
      Looking forward to speaking with you soon.
  </p>

@endsection

