@extends('emails.layout')

@section('title', 'Training Evaluation Result - Passed')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Training Evaluation Result – {{ $application->job->job_title }}
  </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
      Dear {{ $user->first_name }} {{ $user->last_name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        We are pleased to inform you that you have successfully passed the training evaluation for the position of <strong>{{ $application->job->job_title }}</strong> at <strong>{{$application->job->company_name}}</strong>.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
      style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
      <tr>
        <td style="padding:14px 16px;">
              <h3 style="margin:0 0 12px; font-size:18px; color:#0f172a;">Below is your evaluation result:</h3>

              <p style="margin:0 0 8px; font-size:16px;">I. Knowledge & Understanding: <strong>{{ $knowledge }}</strong>/30</p>
              <p style="margin:0 0 8px; font-size:16px;">II. Skill Application: <strong>{{ $skill }}</strong>/30</p>
              <p style="margin:0 0 16px; font-size:16px;">III. Participation & Engagement: <strong>{{ $participation }}</strong>/20</p>
              <p style="margin:0 0 16px; font-size:16px;">IV. Professionalism & Attitude: <strong>{{ $professionalism }}</strong>/20 </p>
              <p style="margin:0 0 16px; font-size:16px;">Total Score: {{ $totalScore }}/100</p>  
              <p style="margin:0 0 16px; font-size:16px;">Result: <strong>Passed</strong> ✅</p>
        </td>
      </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        Your performance during the training demonstrated strong understanding, skill application, and professional conduct. You are now eligible to proceed with the next step — finalizing your employment contract. Please wait for an email regarding your employment contract.
    </p>

    <p style="margin:0; line-height:1.6;">
        Congratulations on your achievement, and we look forward to officially welcoming you to the team.
    </p>
@endsection
