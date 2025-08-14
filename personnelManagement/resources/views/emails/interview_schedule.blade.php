@extends('emails.layout')

@section('title', 'Interview Schedule')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Interview Schedule – {{ $interview->application->job->job_title }}
  </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
      Dear {{ $interview->applicant->first_name }} {{ $interview->applicant->last_name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
      Thank you for your interest in joining Vills Manpower Recruitment Agency and for submitting your application for the position of 
      <strong>{{ $interview->application->job->job_title ?? 'N/A' }}</strong>.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
      We are pleased to inform you that your application has been shortlisted. We would like to invite you for an interview to further discuss your application.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
      style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
      <tr>
        <td style="padding:14px 16px;">
              <h3 style="margin:20px 0 12px; font-size:18px; color:#0f172a;"><strong>Interview Details:</strong></h3>

              <p style="margin:0 0 8px; font-size:16px;">📅 Date: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('F d, Y') }}</strong></p>
              <p style="margin:0 0 8px; font-size:16px;">🕙 Time: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('h:i A') }}</strong></p>
              <p style="margin:0 0 16px; font-size:16px;">📍 Location: <strong>JKMA BUILDING CHECKPOINT PACIANO CALAMBA LAGUNA</strong></p>

              <p style="margin:0 0 16px; line-height:1.6;">
                Please be prepared with any supporting documents, such as a valid ID and a copy of your updated résumé.
              </p>
        </td>
      </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
      If you have any questions or need to reschedule, feel free to contact us at 
      <a href="mailto:villsmanpower@gmail.com" style="color:#2563eb; text-decoration:none;">villsmanpower@gmail.com</a>.
    </p>

    <p style="margin:0; line-height:1.6;">
      We look forward to meeting you!
    </p>
    
@endsection
