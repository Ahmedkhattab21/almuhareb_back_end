<?php

namespace Tests\Unit;

use App\Services\Otp\StaticOtpService;
use Tests\TestCase;

class StaticOtpServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.static_otp.enabled' => true,
            'services.static_otp.code' => '9876',
            'services.static_otp.allow_all' => false,
            'services.static_otp.allowed_phones' => ['966501234567'],
            'services.static_otp.expires_at' => now()->addDay()->toDateTimeString(),
        ]);
    }

    public function test_it_sends_for_allowed_phone(): void
    {
        $result = app(StaticOtpService::class)->sendOtp('966501234567', 'Ar');

        $this->assertTrue($result->successful);
        $this->assertNotEmpty($result->providerRequestId);
    }

    public function test_it_rejects_phone_outside_allowlist(): void
    {
        $result = app(StaticOtpService::class)->sendOtp('966509999999', 'Ar');

        $this->assertFalse($result->successful);
    }

    public function test_it_can_allow_all_valid_phones(): void
    {
        config(['services.static_otp.allow_all' => true]);

        $result = app(StaticOtpService::class)->sendOtp('966509999999', 'Ar');

        $this->assertTrue($result->successful);
    }

    public function test_it_verifies_configured_code(): void
    {
        $result = app(StaticOtpService::class)->verifyOtp('request-id', '9876', 'Ar', '966501234567');

        $this->assertTrue($result->successful);
    }

    public function test_it_rejects_different_code(): void
    {
        $result = app(StaticOtpService::class)->verifyOtp('request-id', '1234', 'Ar', '966501234567');

        $this->assertFalse($result->successful);
        $this->assertSame('invalid_code', $result->error);
    }

    public function test_it_rejects_after_static_expiry(): void
    {
        config(['services.static_otp.expires_at' => now()->subMinute()->toDateTimeString()]);

        $result = app(StaticOtpService::class)->sendOtp('966501234567', 'Ar');

        $this->assertFalse($result->successful);
    }

    public function test_it_rejects_production_static_without_expiry(): void
    {
        config(['services.static_otp.expires_at' => null]);
        $this->app->detectEnvironment(fn () => 'production');

        $result = app(StaticOtpService::class)->sendOtp('966501234567', 'Ar');

        $this->assertFalse($result->successful);
    }
}
