<?php

return [
    'validation' => [
        'phone_required' => 'Phone number is required.',
        'phone_exists' => 'This phone number is not registered.',
        'phone_invalid' => 'Invalid phone number format.',
        'code_required' => 'Verification code is required.',
        'code_digits' => 'Verification code must be 4 to 8 digits.',
    ],

    'messages' => [
        'code_sent' => 'Verification code has been sent to your phone number.',
        'resend_wait' => 'A verification code has already been sent. Please try again shortly.',
        'too_many_sends' => 'Verification code was requested too many times. Please try later.',
        'otp_provider_unavailable' => 'Could not send verification code right now. Please try again.',
        'account_inactive' => 'This account is currently inactive. Please contact support.',
        'no_valid_code' => 'No valid verification code found. Please request a new code.',
        'code_expired' => 'Verification code has expired. Please request a new code.',
        'too_many_attempts' => 'Too many attempts. Please request a new code.',
        'invalid_code' => 'Invalid verification code.',
        'login_success' => 'Logged in successfully.',
        'profile_loaded' => 'Worker profile loaded successfully.',
        'logout_success' => 'Logged out successfully.',
    ],

    'sms' => [
        'login_code' => 'Your login code is: :code',
    ],
];
