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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'key' => env('GEMINI_API_KEY'),

    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),

    'tts_model' => env('GEMINI_TTS_MODEL', 'gemini-2.5-flash-preview-tts'),
    'tts_voice' => env('GEMINI_TTS_VOICE', 'Kore'),

    'timeout' => env('GEMINI_TIMEOUT', 120),
    'inline_attachment_max_bytes' => env('GEMINI_INLINE_ATTACHMENT_MAX_BYTES', 15 * 1024 * 1024),
],
    'firebase' => [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS'),
    'android_channel_id' => env('FIREBASE_ANDROID_CHANNEL_ID', 'default'),
],

    'msegat' => [
        'enabled' => filter_var(env('MSEGAT_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'base_url' => env('MSEGAT_BASE_URL', 'https://www.msegat.com/gw'),
        'username' => env('MSEGAT_USERNAME'),
        'api_key' => env('MSEGAT_API_KEY'),
        'sender' => env('MSEGAT_SENDER'),
        'default_language' => env('MSEGAT_DEFAULT_LANGUAGE', 'Ar'),
        'connect_timeout' => (int) env('MSEGAT_CONNECT_TIMEOUT', 5),
        'timeout' => (int) env('MSEGAT_TIMEOUT', 15),
    ],

    'otp' => [
        'provider' => env('OTP_PROVIDER', 'msegat'),
        'resend_after_seconds' => (int) env('OTP_RESEND_AFTER_SECONDS', 60),
        'expires_in_minutes' => (int) env('OTP_EXPIRES_IN_MINUTES', 5),
        'max_verify_attempts' => (int) env('OTP_MAX_VERIFY_ATTEMPTS', 5),
        'max_sends_per_hour' => (int) env('OTP_MAX_SENDS_PER_HOUR', 5),
    ],

    'static_otp' => [
        'enabled' => filter_var(env('OTP_STATIC_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'code' => env('OTP_STATIC_CODE'),
        'allow_all' => filter_var(env('OTP_STATIC_ALLOW_ALL', false), FILTER_VALIDATE_BOOLEAN),
        'allowed_phones' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('OTP_STATIC_ALLOWED_PHONES', ''))
        ))),
        'expires_at' => env('OTP_STATIC_EXPIRES_AT'),
    ],

];
