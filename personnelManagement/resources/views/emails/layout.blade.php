<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>@yield('title')</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
        }

        .content p:last-child {
            margin-bottom: 0 !important;
        }


        @media (max-width: 620px) {
            .container { width: 100% !important; }
            .content { padding: 20px !important; }
        }
    </style>
</head>
<body>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f4f6f8;">
        <tr>
            <td align="center" style="padding:24px;">
                <table class="container" role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" 
                       style="width:600px; max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#BD9168; padding:20px 24px;">
                            <h1 style="margin:0; color:#ffffff; font-size:20px; font-weight:600;">
                                Vills Manpower Agency • Recruitment
                            </h1>
                        </td>
                    </tr>

                    <!-- Dynamic Content -->
                    <tr>
                        <td class="content" style="padding:28px; padding-bottom:0;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td class="signature" style="padding:20px 28px 28px;">
                            <p style="color:#475569;">
                                Best regards,<br/>
                                <strong>HR Management Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 24px; background:#f1f5f9; text-align:center; font-size:12px; color:#6b7280;">
                            © {{ date('Y') }} Vills Manpower Recruitment Agency. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
