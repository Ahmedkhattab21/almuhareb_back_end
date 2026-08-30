# MSEGAT OTP Mobile Integration

## Summary

Worker login still uses the existing mobile flow:

1. The worker enters a phone number.
2. The backend sends an OTP through MSEGAT.
3. The worker enters the OTP.
4. The backend verifies the OTP with MSEGAT.
5. The backend returns the existing Sanctum token response.

Flutter must not call MSEGAT directly and does not need `MSEGAT_USERNAME`, `MSEGAT_API_KEY`, or `MSEGAT_SENDER`.

## Base URL

Production: `https://myaman.io/api`

Local: `http://127.0.0.1:8080/api`

## Send OTP

Method: `POST`

Path: `/worker/login/request-code`

Headers:

```http
Accept: application/json
Content-Type: application/json
Accept-Language: ar-EG
```

Request body:

```json
{
  "phone": "0501234567"
}
```

Supported phone inputs:

- `05XXXXXXXX`
- `5XXXXXXXX`
- `+9665XXXXXXXX`
- `009665XXXXXXXX`
- `9665XXXXXXXX`

The backend sends MSEGAT the normalized format: `9665XXXXXXXX`.

Success response:

```json
{
  "status": true,
  "message": "تم إرسال كود التحقق إلى رقم الهاتف.",
  "data": {
    "masked_phone": "9665******67",
    "resend_after": 60,
    "expires_in": 300
  }
}
```

Validation error:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "phone": ["صيغة رقم الجوال غير صحيحة."]
  }
}
```

Rate limit:

```json
{
  "status": false,
  "message": "تم طلب رمز التحقق عدة مرات. حاول لاحقاً."
}
```

Provider unavailable:

```json
{
  "status": false,
  "message": "تعذر إرسال رمز التحقق حالياً. حاول مرة أخرى."
}
```

## Verify OTP

Method: `POST`

Path: `/worker/login/verify-code`

Headers:

```http
Accept: application/json
Content-Type: application/json
Accept-Language: ar-EG
```

Request body:

```json
{
  "phone": "0501234567",
  "code": "1234",
  "fcm_token": "optional-fcm-token"
}
```

`code` accepts 4 to 8 digits.

Success response keeps the old token contract:

```json
{
  "status": true,
  "message": "تم تسجيل الدخول بنجاح.",
  "data": {
    "token": "SANCTUM_TOKEN",
    "token_type": "Bearer",
    "worker": {
      "id": 1,
      "name": "Worker Name",
      "email": "worker@example.com",
      "phone": "0501234567",
      "company_id": 1,
      "status": "active",
      "fcm_token": "optional-fcm-token"
    }
  }
}
```

Invalid OTP:

```json
{
  "status": false,
  "message": "كود التحقق غير صحيح."
}
```

Expired OTP:

```json
{
  "status": false,
  "message": "انتهت صلاحية كود التحقق. يرجى طلب كود جديد."
}
```

Too many verify attempts:

```json
{
  "status": false,
  "message": "تم تجاوز عدد المحاولات المسموح. يرجى طلب كود جديد."
}
```

## Language

Flutter can send language by:

- `Accept-Language`
- `lang` header
- `X-Language` header

OTP SMS language mapping:

- Arabic locales like `ar`, `ar-EG`, `ar-SA` => `Ar`
- All other locales => `En`

This mapping is only for the OTP SMS. It does not change the worker preferred language.

## Compatibility

No endpoint changed.

No existing required field changed.

New optional send response fields:

- `data.masked_phone`
- `data.resend_after`
- `data.expires_in`

Removed from API:

- `debug_code`

The fixed test code `1111` is not accepted as a backend bypass anymore.

## Flutter Handling

Handle these HTTP statuses:

- `200`: OTP sent or verified.
- `422`: validation error, invalid OTP, expired OTP, or no pending OTP.
- `429`: resend cooldown, hourly send limit, or too many verify attempts.
- `503`: MSEGAT unavailable or not configured.

## cURL

```bash
curl -X POST "https://myaman.io/api/worker/login/request-code" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar-EG" \
  -d '{"phone":"0501234567"}'
```

```bash
curl -X POST "https://myaman.io/api/worker/login/verify-code" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: ar-EG" \
  -d '{"phone":"0501234567","code":"1234","fcm_token":"optional"}'
```

## End-to-End Test Steps

1. Enter a registered worker phone number.
2. Call `/worker/login/request-code`.
3. Wait for the SMS.
4. Enter the OTP from SMS.
5. Call `/worker/login/verify-code`.
6. Confirm token exists in `data.token`.
7. Try the same OTP again and confirm it fails.
8. Try resend before 60 seconds and confirm it returns `429`.

## رسالة مختصرة لمطور Flutter

تم ربط OTP من الباك إند مع MSEGAT. استخدم نفس endpoint الحالي لإرسال الكود والتحقق. لا تستدعي MSEGAT من التطبيق ولا تحتاج أي credentials. ابعت `phone` فقط في الإرسال، و`phone + code + fcm_token optional` في التحقق. اعرض `resend_after` و`expires_in` من response الإرسال. الكود من 4 إلى 8 أرقام. ابعت لغة التطبيق في `Accept-Language`: العربي يرسل SMS عربي، وباقي اللغات SMS إنجليزي. `debug_code` اتشال وكود `1111` لم يعد يعمل كبايباس.
