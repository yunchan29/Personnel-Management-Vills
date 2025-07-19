<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interview Rescheduled</title>
</head>
<body>
    <div class="email-container">
        <h1>Interview Rescheduled</h1>

       <p>Dear {{ $interview->applicant->first_name }} {{ $interview->applicant->last_name }},</p> 


       <p>We hope you’re doing well. We would like to inform you that we’ve rescheduled your interview due to some internal adjustments in our team’s availability.</p>

        <p><strong>New Interview Schedule:</strong></p>

        <p>📅 Date: {{ \Carbon\Carbon::parse($interview->scheduled_at)->format('F j, Y') }}</p>  
        <p>⏰ Time: {{ \Carbon\Carbon::parse($interview->scheduled_at)->format('h:i A') }}</p>  

        <p>We appreciate your flexibility and understanding regarding this change. Please let us know if you have any concerns about the new schedule.</p>

        <p>Looking forward to speaking with you soon.</p>

        <p>Best regards,</p>
        <p>HR Management Team</p>
        <br>
        <p class="footer">
            If you have any questions or concerns, feel free to reply to this email.<br>
            Thank you,<br>
            Vills Manpower Recruitment Agency
        </p>
    </div>
</body>
</html>








