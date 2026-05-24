<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #111827;">
    <h2 style="margin-bottom: 12px;">Login verification code</h2>

    <p>Hello {{ $user->name }},</p>
    <p>Use the following one-time code to complete your login:</p>

    <div style="font-size: 28px; font-weight: 700; letter-spacing: 6px; margin: 24px 0; color: #4338ca;">
        {{ $code }}
    </div>

    <p>This code will expire in {{ config('mfa.otp.ttl_minutes', 10) }} minutes.</p>
    <p>If you did not try to sign in, please change your password immediately.</p>
</div>
