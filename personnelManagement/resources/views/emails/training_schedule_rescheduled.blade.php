<!DOCTYPE html>
<html>
<head>
    <title>Training Schedule Rescheduled</title>
</head>
<body>
    <h2>Hello {{ $schedule->user->name }},</h2>

    <p>This is to inform you that your training schedule has been <strong>rescheduled</strong>. The new training dates are as follows:</p>

    <ul>
        <li><strong>New Start Date:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('F j, Y') }}</li>
        <li><strong>New End Date:</strong> {{ \Carbon\Carbon::parse($schedule->end_date)->format('F j, Y') }}</li>
    </ul>

    <p>Please note the changes and adjust your availability accordingly.</p>

    <p>For any concerns, feel free to contact us.</p>

    <p>Best regards,</p>
    <p><strong>Your HR Team</strong></p>
</body>
</html>
