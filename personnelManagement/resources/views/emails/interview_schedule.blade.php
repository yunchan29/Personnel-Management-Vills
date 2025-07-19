<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Interview Schedule</title>
</head>
<body>
    <p>Dear {{ $interview->applicant->first_name }} {{ $interview->applicant->last_name }},</p>

    <p>Thank you for your interest in joining Vills Manpower Recruitment Agency and for submitting your application for the position of {{ $interview->application->job->job_title ?? 'N/A' }}.</p>

    <p>We are pleased to inform you that your application has been shortlisted. We would like to invite you for an interview to further discuss your application.</p>
    
    <h2><strong>Interview Details:<strong></h2>

    <p>📅 Date: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('F d, Y') }}</strong></p>

    <p>🕙 Time: <strong>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('h:i A') }}</strong></p>

    <p>📍 Location: <strong>JKMA BUILDING CHECKPOINT PACIANO CALAMBA LAGUNA<strong></p>

    <p>Please be prepared with any supporting documents, such as a valid ID and a copy of your updated résumé.</p>

    <p>If you have any questions or need to reschedule, feel free to contact us at villsmanpower@gmail.com.</p>

    <p>We look forward to meeting you!</p>

    <p>Best regards,</p>
    <p>HR Management Team</p>
</body>
</html>


























