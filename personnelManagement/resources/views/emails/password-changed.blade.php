<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Changed Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f2f5; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="color: #333333; margin-bottom: 20px;">Password Changed Successfully</h2>

        <p style="color: #555555; line-height: 1.6;">
            Hello {{ $user->first_name }},
        </p>

        <p style="color: #555555; line-height: 1.6;">
            This is a confirmation that your password was successfully changed on {{ $timestamp }}.
        </p>

        <div style="background-color: #e7f3ff; padding: 15px; border-left: 4px solid #0d6efd; margin: 20px 0;">
            <p style="margin: 0; color: #555555;">
                <strong>Account:</strong> {{ $user->email }}<br>
                <strong>Changed on:</strong> {{ $timestamp }}
            </p>
        </div>

        <p style="color: #555555; line-height: 1.6;">
            If you did not make this change, please contact our support team immediately or reset your password to secure your account.
        </p>

        <p style="margin-top: 40px; color: #555555;">
            Regards,<br>
            <strong>Personnel Management Team</strong>
        </p>
    </div>
</body>
</html>
