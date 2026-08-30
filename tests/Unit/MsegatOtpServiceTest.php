<?php

namespace Tests\Unit;

use App\Services\Otp\MsegatOtpService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MsegatOtpServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.msegat.enabled' => true,
            'services.msegat.base_url' => 'https://www.msegat.com/gw',
            'services.msegat.username' => 'user',
            'services.msegat.api_key' => 'secret',
            'services.msegat.sender' => 'AMAN',
            'services.msegat.connect_timeout' => 5,
            'services.msegat.timeout' => 15,
        ]);
    }

    public function test_it_sends_otp_with_expected_payload(): void
    {
        Http::fake([
            'www.msegat.com/gw/sendOTPCode.php' => Http::response(['code' => 1, 'id' => 'REQ-1'], 200),
        ]);

        $result = app(MsegatOtpService::class)->sendOtp('966501234567', 'ar-EG');

        $this->assertTrue($result->successful);
        $this->assertSame('REQ-1', $result->providerRequestId);

        Http::assertSent(fn ($request) => $request->url() === 'https://www.msegat.com/gw/sendOTPCode.php'
            && $request['lang'] === 'Ar'
            && $request['userName'] === 'user'
            && $request['number'] === '966501234567'
            && $request['apiKey'] === 'secret'
            && $request['userSender'] === 'AMAN');
    }

    public function test_it_uses_english_for_non_arabic_otp_messages(): void
    {
        $this->assertSame('En', app(MsegatOtpService::class)->msegatLanguage('en-US'));
        $this->assertSame('En', app(MsegatOtpService::class)->msegatLanguage('hi'));
    }

    public function test_send_fails_when_provider_success_has_no_id(): void
    {
        Http::fake([
            'www.msegat.com/gw/sendOTPCode.php' => Http::response(['code' => 1], 200),
        ]);

        $result = app(MsegatOtpService::class)->sendOtp('966501234567', 'en-US');

        $this->assertFalse($result->successful);
        $this->assertSame('send_failed', $result->error);
    }

    public function test_verify_otp_uses_provider_request_id_and_code(): void
    {
        Http::fake([
            'www.msegat.com/gw/verifyOTPCode.php' => Http::response(['code' => 1], 200),
        ]);

        $result = app(MsegatOtpService::class)->verifyOtp('REQ-1', '1234', 'En');

        $this->assertTrue($result->successful);

        Http::assertSent(fn ($request) => $request->url() === 'https://www.msegat.com/gw/verifyOTPCode.php'
            && $request['id'] === 'REQ-1'
            && $request['code'] === '1234');
    }

    public function test_it_handles_connection_errors(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $result = app(MsegatOtpService::class)->sendOtp('966501234567', 'ar-EG');

        $this->assertFalse($result->successful);
        $this->assertSame('connection_error', $result->providerCode);
    }
}
