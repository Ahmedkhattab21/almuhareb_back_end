<?php

namespace Tests\Feature;

use App\Models\WorkerLoginOtp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StaticOtpFlowTest extends TestCase
{
    use DatabaseTransactions;

    private string $phone = '0501234567';
    private string $normalizedPhone = '966501234567';
    private string $code = '9876';

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        config([
            'services.otp.provider' => 'static',
            'services.otp.resend_after_seconds' => 60,
            'services.otp.expires_in_minutes' => 5,
            'services.otp.max_verify_attempts' => 5,
            'services.otp.max_sends_per_hour' => 5,
            'services.static_otp.enabled' => true,
            'services.static_otp.code' => $this->code,
            'services.static_otp.allow_all' => false,
            'services.static_otp.allowed_phones' => [$this->normalizedPhone],
            'services.static_otp.expires_at' => now()->addDay()->toDateTimeString(),
            'services.msegat.enabled' => false,
        ]);

        $this->createWorker();
    }

    public function test_it_sends_static_otp_for_allowed_phone_without_exposing_code(): void
    {
        $response = $this->postJson('/api/worker/login/request-code', [
            'phone' => $this->phone,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => ['masked_phone', 'resend_after', 'expires_in']])
            ->assertJsonMissing(['debug_code' => $this->code])
            ->assertJsonMissing(['otp' => $this->code]);

        $this->assertDatabaseHas('worker_login_otps', [
            'phone' => $this->normalizedPhone,
            'provider' => 'static',
            'status' => 'pending',
        ]);

        Http::assertNothingSent();
    }

    public function test_it_rejects_phone_outside_static_allowlist(): void
    {
        $this->createWorker('0509999999');

        $response = $this->postJson('/api/worker/login/request-code', [
            'phone' => '0509999999',
        ]);

        $response->assertStatus(503);
    }

    public function test_it_allows_all_phones_when_static_allow_all_is_enabled(): void
    {
        config(['services.static_otp.allow_all' => true]);
        $this->createWorker('0509999999');

        $response = $this->postJson('/api/worker/login/request-code', [
            'phone' => '0509999999',
        ]);

        $response->assertOk()->assertJsonPath('status', true);
    }

    public function test_it_verifies_static_otp_and_issues_token_once(): void
    {
        $this->postJson('/api/worker/login/request-code', ['phone' => $this->phone])->assertOk();

        $response = $this->postJson('/api/worker/login/verify-code', [
            'phone' => $this->phone,
            'code' => $this->code,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => ['token', 'token_type', 'worker']]);

        $this->assertDatabaseHas('worker_login_otps', [
            'phone' => $this->normalizedPhone,
            'provider' => 'static',
            'status' => 'verified',
        ]);

        $this->postJson('/api/worker/login/verify-code', [
            'phone' => $this->phone,
            'code' => $this->code,
        ])->assertStatus(422);
    }

    public function test_wrong_static_code_increments_attempts(): void
    {
        $this->postJson('/api/worker/login/request-code', ['phone' => $this->phone])->assertOk();

        $this->postJson('/api/worker/login/verify-code', [
            'phone' => $this->phone,
            'code' => '1234',
        ])->assertStatus(422);

        $this->assertSame(1, WorkerLoginOtp::where('phone', $this->normalizedPhone)->latest()->value('attempts'));
    }

    public function test_expired_static_otp_is_rejected(): void
    {
        $this->postJson('/api/worker/login/request-code', ['phone' => $this->phone])->assertOk();

        WorkerLoginOtp::where('phone', $this->normalizedPhone)->latest()->first()->update([
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/worker/login/verify-code', [
            'phone' => $this->phone,
            'code' => $this->code,
        ])->assertStatus(422);
    }

    public function test_static_expiry_blocks_send(): void
    {
        config(['services.static_otp.expires_at' => now()->subMinute()->toDateTimeString()]);

        $this->postJson('/api/worker/login/request-code', [
            'phone' => $this->phone,
        ])->assertStatus(503);
    }

    public function test_static_code_is_not_written_to_logs(): void
    {
        Log::spy();

        $this->postJson('/api/worker/login/request-code', [
            'phone' => $this->phone,
        ])->assertOk();

        Log::shouldHaveReceived('info')->withArgs(function ($message, $context = []) {
            $this->assertStringNotContainsString($this->code, (string) $message);
            $this->assertStringNotContainsString($this->code, json_encode($context));

            return true;
        });
    }

    private function createWorker(?string $phone = null): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'OTP Test Company '.uniqid(),
            'email' => uniqid('otp-company').'@example.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('workers')->insert([
            'company_id' => $companyId,
            'name' => 'OTP Test Worker',
            'email' => uniqid('otp-worker').'@example.com',
            'phone' => $phone ?? $this->phone,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
