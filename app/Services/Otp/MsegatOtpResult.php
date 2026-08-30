<?php

namespace App\Services\Otp;

class MsegatOtpResult
{
    public function __construct(
        public bool $successful,
        public ?string $providerRequestId = null,
        public ?string $providerCode = null,
        public ?string $error = null,
        public int $httpStatus = 200,
    ) {
    }

    public static function success(?string $providerRequestId, ?string $providerCode = null, int $httpStatus = 200): self
    {
        return new self(true, $providerRequestId, $providerCode, null, $httpStatus);
    }

    public static function failure(?string $providerCode = null, ?string $error = null, int $httpStatus = 503): self
    {
        return new self(false, null, $providerCode, $error, $httpStatus);
    }
}
