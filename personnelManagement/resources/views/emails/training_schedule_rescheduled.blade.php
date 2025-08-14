@extends('emails.layout')
@section('title', 'Training Schedule Set')

@section('content')
    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Training Reschedule – {{ $application->job->job_title }}
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $application->user->first_name ?? $application->user->name }} {{ $application->user->last_name ?? $application->user->name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        I hope this message finds you well.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We would like to inform you that the training session originally scheduled will be rescheduled.
    </p>


        <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
        style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
        <tr>
        <td style="padding:14px 16px;">
            <p style="margin:0; font-size:14px; color:#9a3412;">
               The new training schedule is as follows:
            </p>
            <ul>
                <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('F j, Y') }}</li>
                <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($schedule->end_date)->format('F j, Y') }}</li>
            </ul>
        </td>
        </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        We apologize for any inconvenience this change may cause and appreciate your understanding. Please confirm your availability for the new schedule at your earliest convenience.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Should you have any questions or concerns, feel free to reply to this email.
    </p>

    <p style="margin:0; line-height:1.6;">
      Thank you for your cooperation and continued commitment to your professional development.
    </p>
@endsection