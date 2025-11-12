@extends('emails.layout')

@section('title', 'Verify Your Email Address')

@section('content')
  <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
    Verify Your Email Address
  </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
      Dear {{ $user->first_name }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
      Thank you for registering with Vills Manpower Personnel Management System.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
      To complete your registration, please use the verification code below:
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
      style="margin:16px 0; background:#f0fdf4; border:1px solid #86efac; border-radius:6px;">
      <tr>
        <td style="padding:20px; text-align:center;">
          <p style="margin:0 0 8px; font-size:14px; color:#166534; text-transform:uppercase; letter-spacing:0.5px;">
            Verification Code
          </p>
          <p style="margin:0; font-size:32px; font-weight:bold; color:#15803d; letter-spacing:4px; font-family:monospace;">
            {{ $code }}
          </p>
        </td>
      </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6; color:#dc2626; font-weight:500;">
      ⏱️ This code will expire in 15 minutes.
    </p>

    <p style="margin:0; line-height:1.6;">
      If you did not create an account, no further action is required.
    </p>

@endsection
