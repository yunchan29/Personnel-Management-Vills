@extends('emails.layout')

@section('title', 'Position Filled')

@section('content')
    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Application Update – {{ $job->job_title }}
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Thank you for your interest in the position of <strong>{{ $job->job_title }}</strong> at <strong>{{ $job->company_name }}</strong>.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
        <tr>
        <td style="padding:14px 16px;">
            <p style="margin:0; font-size:14px; color:#9a3412;">
                We regret to inform you that the position has now been filled. All available vacancies for this role have been taken.
            </p>
        </td>
        </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        We sincerely appreciate the time and effort you invested in the application process. Your qualifications and interest in joining our team are valued.
    </p>

    <p style="margin:0; line-height:1.6;">
        We encourage you to explore other opportunities with us. Please visit our job portal regularly for new openings that match your skills and experience.
    </p>

@endsection
