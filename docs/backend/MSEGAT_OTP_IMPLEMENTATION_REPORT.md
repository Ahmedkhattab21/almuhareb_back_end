# MSEGAT OTP Backend Implementation Report

## Files Created

- `app/Services/Otp/MsegatOtpService.php`
- `app/Services/Otp/MsegatOtpResult.php`
- `app/Services/Otp/PhoneNumberNormalizer.php`
- `app/Services/Otp/OtpProviderManager.php`
- `app/Services/Otp/StaticOtpService.php`
- `database/migrations/2026_08_30_000003_add_msegat_fields_to_worker_login_otps.php`
- `tests/Unit/MsegatOtpServiceTest.php`
- `tests/Unit/PhoneNumberNormalizerTest.php`
- `tests/Unit/StaticOtpServiceTest.php`
- `tests/Feature/StaticOtpFlowTest.php`
- `docs/mobile/MSEGAT_OTP_MOBILE_INTEGRATION.md`
- `docs/backend/MSEGAT_OTP_IMPLEMENTATION_REPORT.md`

## Files Modified

- `.env.example`
- `config/services.php`
- `app/Http/Controllers/Api/Worker/WorkerAuthController.php`
- `app/Models/WorkerLoginOtp.php`
- `resources/lang/ar/worker_auth.php`
- `resources/lang/en/worker_auth.php`

## Routes Used

- `POST /api/worker/login/request-code`
- `POST /api/worker/login/verify-code`

No duplicate endpoints were created.

## Migration

The existing `worker_login_otps` table is reused.

Added columns:

- `provider`
- `provider_request_id`
- `language`
- `status`
- `verified_at`
- `invalidated_at`
- `metadata`

Added indexes:

- `phone, status, created_at`
- `provider, provider_request_id`

Existing `code_hash` is preserved for backward compatibility, but the raw OTP is no longer generated or stored by the backend.

## Environment Variables

No credentials were committed.

Required variables:

```env
OTP_PROVIDER=msegat
MSEGAT_ENABLED=true
MSEGAT_BASE_URL=https://www.msegat.com/gw
MSEGAT_USERNAME=
MSEGAT_API_KEY=
MSEGAT_SENDER=
MSEGAT_DEFAULT_LANGUAGE=Ar
MSEGAT_CONNECT_TIMEOUT=5
MSEGAT_TIMEOUT=15
OTP_RESEND_AFTER_SECONDS=60
OTP_EXPIRES_IN_MINUTES=5
OTP_MAX_VERIFY_ATTEMPTS=5
OTP_MAX_SENDS_PER_HOUR=5

OTP_STATIC_ENABLED=false
OTP_STATIC_CODE=
OTP_STATIC_ALLOW_ALL=false
OTP_STATIC_ALLOWED_PHONES=
OTP_STATIC_EXPIRES_AT=
```

## Temporary Static OTP Mode

Static mode is enabled by setting:

```env
OTP_PROVIDER=static
OTP_STATIC_ENABLED=true
OTP_STATIC_CODE=
OTP_STATIC_ALLOW_ALL=false
OTP_STATIC_ALLOWED_PHONES=
OTP_STATIC_EXPIRES_AT=
```

In static mode:

- No request is sent to MSEGAT.
- No `debug_code` is returned.
- The OTP record is saved with `provider=static`, a UUID `provider_request_id`, and normal pending status.
- Verification compares the submitted code against the configured backend value using `hash_equals`.
- The temporary code is not written directly in `WorkerAuthController`.
- Production requires a valid `OTP_STATIC_EXPIRES_AT` while static mode is enabled.

Switch back to live MSEGAT by setting `OTP_PROVIDER=msegat`.

## Phone Normalization

Supported Saudi input formats:

- `05XXXXXXXX`
- `5XXXXXXXX`
- `+9665XXXXXXXX`
- `009665XXXXXXXX`
- `9665XXXXXXXX`

MSEGAT receives `9665XXXXXXXX`.

The stored worker phone is not changed. The controller searches using safe candidates to support both local and international stored formats.

## MSEGAT Integration

Send endpoint:

- `POST https://www.msegat.com/gw/sendOTPCode.php`

Verify endpoint:

- `POST https://www.msegat.com/gw/verifyOTPCode.php`

Requests are sent as JSON with:

- `Accept: application/json`
- `Content-Type: application/json`

The backend checks HTTP success, provider code, and send `id`.

The `id` returned from send is stored in `worker_login_otps.provider_request_id` and is used during verification.

## Resend and Attempts

- Resend cooldown: `OTP_RESEND_AFTER_SECONDS`, default `60`.
- Max sends per hour per phone: `OTP_MAX_SENDS_PER_HOUR`, default `5`.
- IP send throttle: 20 per hour.
- Max verify attempts: `OTP_MAX_VERIFY_ATTEMPTS`, default `5`.
- OTP expiry: `OTP_EXPIRES_IN_MINUTES`, default `5`.
- Previous pending OTP rows for the same normalized phone are invalidated when a new code is sent.
- Verified OTP rows are marked `verified` and cannot be reused.

## Error Handling

Client-safe messages are returned. Provider raw errors, API key, and OTP code are not returned to Flutter.

Safe logs include:

- endpoint type
- masked phone
- HTTP status
- provider code

Logs do not include:

- MSEGAT API key
- OTP code

## Current Flow Compatibility

Token creation still uses Sanctum:

- `createToken('worker-mobile-token')->plainTextToken`

Response keeps:

- `data.token`
- `data.token_type`
- `data.worker`

Added optional fields to send response:

- `data.masked_phone`
- `data.resend_after`
- `data.expires_in`

Removed:

- `debug_code`

## Documentation Note

The provided Postman documentation URL was requested as the final source, but it was not readable from the local browsing tool during implementation. The implementation follows the exact send and verify payloads supplied in the task.

## Commands Run

```bash
php artisan migrate --force
php artisan test
php -l app/Http/Controllers/Api/Worker/WorkerAuthController.php
php -l app/Models/WorkerLoginOtp.php
php -l app/Services/Otp/PhoneNumberNormalizer.php
php -l app/Services/Otp/MsegatOtpService.php
php -l database/migrations/2026_08_30_000003_add_msegat_fields_to_worker_login_otps.php
```

## Test Result

`php artisan test`: 9 passed.

Covered:

- Saudi phone normalization.
- Invalid phone rejection.
- MSEGAT send payload.
- Arabic/English OTP language mapping.
- Send failure when provider response has no id.
- Verify payload with provider request id and code.
- Connection error handling.
- Static provider without real MSEGAT HTTP calls.
- No real MSEGAT HTTP calls in tests.

## Production Notes

Do not deploy production until real credentials and mobile test are complete.

Before production:

1. Add real MSEGAT values manually to server `.env`.
2. Confirm sender is approved and active.
3. Confirm account has SMS balance.
4. Run `php artisan migrate --force`.
5. Run `php artisan optimize:clear`.
6. Test real SMS with a provided test phone.
7. Verify the static test code is disabled before switching to production MSEGAT.

READY FOR MOBILE TEST

Not ready for production until real SMS and Flutter E2E tests pass.

READY FOR TEMPORARY STATIC OTP TEST
