<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ config('app.name') }}</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px 0;">
        <tr>
            <td align="center">

                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:8px; padding:24px; box-shadow:0 4px 10px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="text-align:center; padding-bottom:16px;">
                            <h2 style="margin:0; color:#1f2937;">
                                🎉 Account Created Successfully
                            </h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="color:#374151; font-size:14px; line-height:1.6;">
                            <p>Hello,</p>

                            <p>
                                Your user account for <strong>{{ config('app.name') }}</strong> has been created successfully.
                                Please find your temporary password below.
                            </p>

                            <!-- Password Box -->
                            <div style="background:#f9fafb; border:1px dashed #2563eb;
                                    padding:14px; text-align:center; margin:20px 0;
                                    font-size:18px; font-weight:bold; color:#2563eb;">
                                {{ $password }}
                            </div>

                            <p>
                                🔐 For your security, please log in and change this password immediately.
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align:center; margin:24px 0;">
                                <a href="{{ url('/login') }}"
                                    style="background:#2563eb; color:#ffffff;
                                      padding:12px 24px; text-decoration:none;
                                      border-radius:6px; display:inline-block;
                                      font-size:14px;">
                                    Log in to Your Account
                                </a>
                            </div>

                            <p style="font-size:13px; color:#6b7280;">
                                If you did not request this account, please ignore this email
                                or contact the system administrator immediately.
                            </p>

                            <p style="margin-top:24px;">
                                Regards,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                </table>

                <!-- Footer -->
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                    <tr>
                        <td style="text-align:center; font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>