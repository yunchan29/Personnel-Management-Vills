<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f2f5; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="color: #333333; margin-bottom: 20px;">Password Reset Request</h2>

        <p style="color: #555555; line-height: 1.6;">
            You are receiving this email because a password reset request was submitted for your account. If you made this request, please click the button below to proceed:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: 500;">
                Reset Password
            </a>
        </div>

        <p style="color: #555555; line-height: 1.6;">
            If you did not request this action, you may safely disregard this message. No further steps are required on your part.
        </p>

        <p style="margin-top: 40px; color: #555555;">
            Regards,<br>
            <strong>Personnel Management Team</strong>
        </p>
    </div>
</body>
</html>
