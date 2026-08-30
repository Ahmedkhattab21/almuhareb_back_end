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
        $this->assertSame('966555237602', $normalizer->normalizeSaudi('0555237602'));
        $this->assertSame('966555237602', $normalizer->normalizeSaudi('٠٥٥٥٢٣٧٦٠٢'));
        $this->assertSame('966555237602', $normalizer->normalizeSaudi('۰۵۵۵۲۳۷۶۰۲'));
    }

    public function test_it_rejects_invalid_phone_numbers(): void
    {
        $this->assertNull((new PhoneNumberNormalizer())->normalizeSaudi('1234'));
    }
}
