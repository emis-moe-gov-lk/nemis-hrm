<?php

return [
    'enabled' => (bool) env('MFA_ENABLED', true),

    'require_all' => (bool) env('MFA_REQUIRE_ALL', false),

    'trusted_devices' => [
        'cookie_name' => env('MFA_TRUSTED_DEVICE_COOKIE', 'trusted_device'),
        'lifetime_days' => (int) env('MFA_TRUSTED_DEVICE_DAYS', 30),
        'max_devices' => (int) env('MFA_TRUSTED_DEVICE_MAX', 5),
    ],

    'otp' => [
        'length' => (int) env('MFA_OTP_LENGTH', 6),
        'ttl_minutes' => (int) env('MFA_OTP_TTL_MINUTES', 10),
        'resend_cooldown_seconds' => (int) env('MFA_OTP_RESEND_COOLDOWN', 60),
        'max_sends' => (int) env('MFA_OTP_MAX_SENDS', 5),
        'max_attempts' => (int) env('MFA_OTP_MAX_ATTEMPTS', 5),
    ],

    'totp' => [
        'issuer' => env('MFA_TOTP_ISSUER', env('APP_NAME', 'EMIS')),
        'digits' => (int) env('MFA_TOTP_DIGITS', 6),
        'period' => (int) env('MFA_TOTP_PERIOD', 30),
        'window' => (int) env('MFA_TOTP_WINDOW', 1),
        'secret_length' => (int) env('MFA_TOTP_SECRET_LENGTH', 20),
        'recovery_codes' => (int) env('MFA_RECOVERY_CODES', 8),
    ],

    'rate_limits' => [
        'login_email_max_attempts' => (int) env('MFA_LOGIN_EMAIL_MAX_ATTEMPTS', 5),
        'login_ip_max_attempts' => (int) env('MFA_LOGIN_IP_MAX_ATTEMPTS', 20),
        'decay_seconds' => (int) env('MFA_LOGIN_DECAY_SECONDS', 60),
    ],
];
