@extends('emails.layout')

@section('title', 'Training Evaluation Result')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Training Evaluation Result – {{ $application->job->job_title }}
  </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
      Dear {{ $user->first_name }} {{ $user->last_name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Thank you for completing the training program for the position of <strong>{{ $application->job->job_title }}</strong> at <strong>{{$application->job->company_name}}</strong>.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        After careful review of your performance, we regret to inform you that you did not meet the required passing score for the training evaluation.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
      style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
      <tr>
        <td style="padding:14px 16px;">
              <h3 style="margin:0 0 12px; font-size:18px; color:#0f172a;">Evaluation Summary:</h3>

              <p style="margin:0 0 8px; font-size:16px;">I. Knowledge & Understanding: <strong>{{ $knowledge }}</strong>/30</p>
              <p style="margin:0 0 8px; font-size:16px;">II. Skill Application: <strong>{{ $skill }}</strong>/30</p>
              <p style="margin:0 0 16px; font-size:16px;">III. Participation & Engagement: <strong>{{ $participation }}</strong>/20</p>
              <p style="margin:0 0 16px; font-size:16px;">IV. Professionalism & Attitude: <strong>{{ $professionalism }}</strong>/20 </p>
              <p style="margin:0 0 16px; font-size:16px;">Total Score: {{ $totalScore }}/100</p>  
              <p style="margin:0 0 16px; font-size:16px;">Result: <strong>Fail</strong> ❌</p>
        </td>
      </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        As per our hiring process, a passing score of 70 or above is required to proceed to the employment contract stage. Unfortunately, this means we will not be moving forward with your application at this time.    
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We sincerely appreciate your time, effort, and participation during the training. We encourage you to continue improving your skills and to consider reapplying for future opportunities with us.
    </p>

    <p style="margin:0; line-height:1.6;">
        We wish you the best in your career journey.
    </p>
@endsection
