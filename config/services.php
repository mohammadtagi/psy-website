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
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'melipayamak' => [
        'username' => env('MELIPAYAMAK_USERNAME'),
        'password' => env('MELIPAYAMAK_PASSWORD'),
        'pattern_id' => (int) env('MELIPAYAMAK_PATTERN_ID', 388165),

        'endpoint' => env(
            'MELIPAYAMAK_ENDPOINT',
            'https://rest.payamak-panel.com/api/SendSMS/SendByBaseNumber'
        ),
    ],

    'otp' => [
        'length' => (int) env('OTP_LENGTH', 6),
        'expires_in' => (int) env('OTP_EXPIRES_IN', 2),
        'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
        'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
