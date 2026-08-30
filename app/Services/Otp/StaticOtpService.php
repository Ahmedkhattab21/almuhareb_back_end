<?php

namespace App\Services\Otp;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StaticOtpService
{
    public function __construct(private PhoneNumberNormalizer $phoneNormalizer)
    {
    }

    public function sendOtp(string $phone, string $language): MsegatOtpResult
    {
        if (! $this->availableFor($phone)) {
            return MsegatOtpResult::failure('static_unavailable', 'provider_unavailable', 503);
        }

        $requestId = (string) Str::uuid();

        Log::info('Static OTP issued', [
            'provider' => 'static',
            'phone' => $this->phoneNormalizer->mask($phone),
            'request_id' => $requestId,
            'language' => $language,
        ]);

        return MsegatOtpResult::success($requestId, 'static');
    }

    public function verifyOtp(string $providerRequestId, string $code, string $language, ?string $phone = null): MsegatOtpResult
    {
        if ($phone && ! $this->availableFor($phone)) {
            return MsegatOtpResult::failure('static_unavailable', 'provider_unavailable', 503);
        }

        $configuredCode = (string) config('services.static_otp.code');

        if ($configuredCode === '') {
            return MsegatOtpResult::failure('static_code_missing', 'provider_unavailable', 503);
        }

        return hash_equals($configuredCode, $code)
            ? MsegatOtpResult::success($providerRequestId, 'static')
            : MsegatOtpResult::failure('static_invalid_code', 'invalid_code', 422);
    }

    public function availableFor(string $phone): bool
    {
        if (! config('services.static_otp.enabled')) {
            return false;
        }

        if ($this->isExpired()) {
            Log::warning('Static OTP disabled by expiration date', [
                'provider' => 'static',
                'phone' => $this->phoneNormalizer->mask($phone),
            ]);

            return false;
        }

        if (App::environment('production') && ! $this->hasValidExpiry()) {
            Log::error('Static OTP production expiry is missing or invalid', [
                'provider' => 'static',
                'phone' => $this->phoneNormalizer->mask($phone),
            ]);

            return false;
        }

        if (config('services.static_otp.allow_all')) {
            return true;
        }

        $allowedPhones = collect(config('services.static_otp.allowed_phones', []))
            ->map(fn (string $allowedPhone) => $this->phoneNormalizer->normalizeSaudi($allowedPhone))
            ->filter()
            ->all();

        return in_array($phone, $allowedPhones, true);
    }

    private function isExpired(): bool
    {
        $expiresAt = $this->expiresAt();

        return $expiresAt && now()->greaterThan($expiresAt);
    }

    private function hasValidExpiry(): bool
    {
        return $this->expiresAt() !== null;
    }

    private function expiresAt(): ?Carbon
    {
        $expiresAt = config('services.static_otp.expires_at');

        if (blank($expiresAt)) {
            return null;
        }

        try {
            return Carbon::parse($expiresAt);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
