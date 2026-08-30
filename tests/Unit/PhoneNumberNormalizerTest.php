<?php

namespace Tests\Unit;

use App\Services\Otp\PhoneNumberNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNumberNormalizerTest extends TestCase
{
    public function test_it_normalizes_supported_saudi_phone_formats(): void
    {
        $normalizer = new PhoneNumberNormalizer();

        $this->assertSame('966501234567', $normalizer->normalizeSaudi('0501234567'));
        $this->assertSame('966501234567', $normalizer->normalizeSaudi('501234567'));
        $this->assertSame('966501234567', $normalizer->normalizeSaudi('+966501234567'));
        $this->assertSame('966501234567', $normalizer->normalizeSaudi('00966501234567'));
        $this->assertSame('966501234567', $normalizer->normalizeSaudi('966501234567'));
    }

    public function test_it_rejects_invalid_phone_numbers(): void
    {
        $this->assertNull((new PhoneNumberNormalizer())->normalizeSaudi('1234'));
    }
}
