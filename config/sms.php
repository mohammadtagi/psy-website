<?php

return [
    // درایور پیش‌فرض: fake برای توسعه، melipayamak برای تولید
    'driver' => env('SMS_DRIVER', 'fake'),

    'from' => env('SMS_FROM', '10008000'),

    'otp' => [
        'ttl_minutes'        => (int) env('OTP_TTL_MINUTES', 5),
        'length'             => (int) env('OTP_LENGTH', 6),
        'max_attempts'       => (int) env('OTP_MAX_ATTEMPTS', 5),
        'max_sends_per_hour' => (int) env('OTP_MAX_SENDS_PER_HOUR', 5),
    ],

    'drivers' => [
        'fake' => [
            'log_channel' => env('SMS_FAKE_LOG_CHANNEL'),
        ],

        'melipayamak' => [
            'username' => env('MELIPAYAMAK_USERNAME'),
            'password' => env('MELIPAYAMAK_PASSWORD'),
            'origin'   => env('MELIPAYAMAK_ORIGIN'),
            'base_uri' => env('MELIPAYAMAK_BASE_URI', 'https://rest.payamak-panel.com/api/SendSMS/'),
        ],
    ],
];
