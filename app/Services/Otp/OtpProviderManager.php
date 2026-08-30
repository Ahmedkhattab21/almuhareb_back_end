<?php

namespace App\Services\Otp;

class OtpProviderManager
{
    public function __construct(
        private MsegatOtpService $msegatOtpService,
        private StaticOtpService $staticOtpService
    ) {
    }

    public function providerName(): string
    {
        return strtolower((string) config('services.otp.provider', 'msegat'));
    }

    public function sendOtp(string $phone, string $language): MsegatOtpResult
    {
        return match ($this->providerName()) {
            'msegat' => $this->msegatOtpService->sendOtp($phone, $language),
            'static' => $this->staticOtpService->sendOtp($phone, $language),
            default => MsegatOtpResult::failure('unsupported_provider', 'provider_unavailable', 503),
        };
    }

    public function verifyOtp(string $provider, string $providerRequestId, string $code, string $language, string $phone): MsegatOtpResult
    {
        return match (strtolower($provider)) {
            'msegat' => $this->msegatOtpService->verifyOtp($providerRequestId, $code, $language),
            'static' => $this->staticOtpService->verifyOtp($providerRequestId, $code, $language, $phone),
            default => MsegatOtpResult::failure('unsupported_provider', 'provider_unavailable', 503),
        };
    }
}
