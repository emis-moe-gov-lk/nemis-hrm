<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message – EMIS</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); padding: 36px 40px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.7); margin: 6px 0 0; font-size: 13px; }
        .body { padding: 36px 40px; }
        .label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
        .value { font-size: 15px; color: #1e293b; font-weight: 500; margin-bottom: 24px; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; }
        .message-box p { margin: 0; font-size: 15px; color: #334155; line-height: 1.7; white-space: pre-wrap; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>📬 New Contact Message</h1>
            <p>Received via the EMIS public portal contact form</p>
        </div>
        <div class="body">
            <div class="label">Full Name</div>
            <div class="value">{{ $senderName }}</div>

            <div class="label">Email Address</div>
            <div class="value"><a href="mailto:{{ $senderEmail }}" style="color:#2563eb;text-decoration:none;">{{ $senderEmail }}</a></div>

            <div class="label">Message</div>
            <div class="message-box">
                <p>{{ $userMessage }}</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} EMIS – National Education Management Information System</p>
        </div>
    </div>
</body>
</html>
