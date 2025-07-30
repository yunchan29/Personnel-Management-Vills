<!DOCTYPE html>
<html>
<head>
    <title>Training Schedule Set</title>
</head>
<body>
    <h2>Hello {{ $schedule->user->name }},</h2>

    <p>We are pleased to inform you that your training schedule has been set as follows:</p>

    <ul>
        <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('F j, Y') }}</li>
        <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($schedule->end_date)->format('F j, Y') }}</li>
    </ul>

    <p>Please make sure to attend the training during the dates mentioned above.</p>

    <p>Should you have any questions, feel free to reach out to us.</p>

    <p>Thank you,</p>
    <p><strong>Your HR Team</strong></p>
</body>
</html>
