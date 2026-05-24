<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a convenational file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'turnstile' => [
        'enabled' => (bool) env('TURNSTILE_ENABLED', false),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret' => env('TURNSTILE_SECRET_KEY'),
    ],

    'mobitel' => [
        'enabled' => (bool) env('SMS_ENABLED', false),
        'url' => env('SMS_GATEWAY_URL'),
        'user' => env('SMS_USER'),
        'password' => env('SMS_PASSWORD'),
        'alias' => env('SMS_ALIAS'),
    ],

    'osrm' => [
        'url' => env('OSRM_URL', 'https://router.project-osrm.org'),
        'timeout' => env('OSRM_TIMEOUT', 5),
        'connect_timeout' => env('OSRM_CONNECT_TIMEOUT', 3),
        'verify_ssl' => env('OSRM_VERIFY_SSL', true),
        'log_failures' => env('OSRM_LOG_FAILURES', false),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'oidc' => [
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => env('OIDC_CLIENT_SECRET'),
        'redirect' => env('OIDC_REDIRECT_URI'),

        // These are the key differences from a standard Socialite provider.
        // We manually specify the OIDC endpoints.
        'authorize_url' => env('OIDC_AUTHORIZE_URL'),
        'token_url' => env('OIDC_TOKEN_URL'),
        'userinfo_url' => env('OIDC_USERINFO_URL'),
        'end_session_url' => env('OIDC_ENDSESSION_URL'),
        'post_logout_redirect_uri' => env('OIDC_POST_LOGOUT_REDIRECT_URI'),
    ],

];
