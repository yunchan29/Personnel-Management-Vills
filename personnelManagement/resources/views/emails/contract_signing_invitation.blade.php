@extends('emails.layout')
@section('title', 'Contract Signing Invitation')

@section('content')
    <h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
        Contract Signing Invitation – {{ $jobTitle }}
    </h2>

    <p style="margin:0 0 16px; line-height:1.6;">
        Dear {{ $applicantName }},
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        Congratulations! We are delighted to inform you that you have successfully passed the training evaluation for the position of <strong>{{ $jobTitle }}</strong> at <strong>{{ $companyName }}</strong>.
    </p>

    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="margin:16px 0; background:#dcfce7; border:1px solid #86efac; border-radius:6px;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0 0 8px; font-size:14px; color:#166534;">
                    You are invited to sign your employment contract on:
                </p>
                <ul style="margin:0; padding-left:20px;">
                    <li><strong>Date:</strong> {{ $signingDate }}</li>
                    <li><strong>Time:</strong> {{ $signingTime }}</li>
                </ul>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px; line-height:1.6;">
        Please make sure to attend on the scheduled date and time. Bring a valid ID and any other documents that may be required.
    </p>

    <p style="margin:0 0 16px; line-height:1.6;">
        This is an important step in finalizing your employment with us. We look forward to welcoming you to our team!
    </p>

    <p style="margin:0; line-height:1.6;">
        If you have any questions or need to reschedule, please contact us as soon as possible.
    </p>
@endsection
