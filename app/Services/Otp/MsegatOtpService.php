<?php

namespace App\Services\Otp;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MsegatOtpService
{
    public function sendOtp(string $phone, string $language): MsegatOtpResult
    {
        if (! $this->configured()) {
            Log::warning('MSEGAT OTP send skipped: missing configuration', [
                'phone' => app(PhoneNumberNormalizer::class)->mask($phone),
            ]);

            return MsegatOtpResult::failure('missing_config', 'provider_unavailable', 503);
        }

        $response = $this->post('sendOTPCode.php', [
            'lang' => $this->msegatLanguage($language),
            'userName' => config('services.msegat.username'),
            'number' => $phone,
            'apiKey' => config('services.msegat.api_key'),
            'userSender' => config('services.msegat.sender'),
        ], 'send', $phone);

        if (! $response['ok']) {
            return MsegatOtpResult::failure($response['code'], 'provider_unavailable', $response['http_status']);
        }

        $requestId = $this->extractRequestId($response['body']);
        $providerCode = $this->extractProviderCode($response['body']);

        if (! $this->isSuccessCode($providerCode) || blank($requestId)) {
            return MsegatOtpResult::failure($providerCode ?: 'missing_id', 'send_failed', 503);
        }

        return MsegatOtpResult::success($requestId, $providerCode, $response['http_status']);
    }

    public function verifyOtp(string $providerRequestId, string $code, string $language): MsegatOtpResult
    {
        if (! $this->configured()) {
            return MsegatOtpResult::failure('missing_config', 'provider_unavailable', 503);
        }

        $response = $this->post('verifyOTPCode.php', [
            'lang' => $this->msegatLanguage($language),
            'userName' => config('services.msegat.username'),
            'apiKey' => config('services.msegat.api_key'),
            'code' => $code,
            'id' => $providerRequestId,
            'userSender' => config('services.msegat.sender'),
        ], 'verify');

        if (! $response['ok']) {
            return MsegatOtpResult::failure($response['code'], 'provider_unavailable', $response['http_status']);
        }

        $providerCode = $this->extractProviderCode($response['body']);

        return $this->isSuccessCode($providerCode)
            ? MsegatOtpResult::success($providerRequestId, $providerCode, $response['http_status'])
            : MsegatOtpResult::failure($providerCode ?: 'verify_failed', 'invalid_code', 422);
    }

    public function msegatLanguage(?string $locale): string
    {
        return Str::startsWith(Str::lower((string) $locale), 'ar') ? 'Ar' : 'En';
    }

    private function post(string $path, array $payload, string $type, ?string $phone = null): array
    {
        $url = rtrim((string) config('services.msegat.base_url'), '/').'/'.$path;
        $safePayload = collect($payload)
            ->except(['apiKey', 'code'])
            ->when(isset($payload['number']), function ($payload) {
                return $payload->put('number', app(PhoneNumberNormalizer::class)->mask((string) $payload['number']));
            })
            ->all();

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout((int) config('services.msegat.connect_timeout', 5))
                ->timeout((int) config('services.msegat.timeout', 15))
                ->post($url, $payload);

            $body = $response->json();
            $providerCode = is_array($body) ? $this->extractProviderCode($body) : null;

            Log::info('MSEGAT OTP response', [
                'type' => $type,
                'phone' => $phone ? app(PhoneNumberNormalizer::class)->mask($phone) : null,
                'http_status' => $response->status(),
                'provider_code' => $providerCode,
                'payload' => $safePayload,
            ]);

            return [
                'ok' => $response->successful() && is_array($body),
                'body' => is_array($body) ? $body : [],
                'code' => $providerCode,
                'http_status' => $response->status(),
            ];
        } catch (ConnectionException|RequestException $e) {
            report($e);
        } catch (Throwable $e) {
            report($e);
        }

        Log::warning('MSEGAT OTP request failed', [
            'type' => $type,
            'phone' => $phone ? app(PhoneNumberNormalizer::class)->mask($phone) : null,
            'payload' => $safePayload,
        ]);

        return [
            'ok' => false,
            'body' => [],
            'code' => 'connection_error',
            'http_status' => 503,
        ];
    }

    private function configured(): bool
    {
        return (bool) config('services.msegat.enabled')
            && filled(config('services.msegat.username'))
            && filled(config('services.msegat.api_key'))
            && filled(config('services.msegat.sender'));
    }

    private function extractRequestId(array $body): ?string
    {
        foreach (['id', 'request_id', 'requestId', 'data.id', 'result.id'] as $key) {
            $value = data_get($body, $key);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private function extractProviderCode(array $body): ?string
    {
        foreach (['code', 'status', 'result.code', 'data.code'] as $key) {
            $value = data_get($body, $key);

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    private function isSuccessCode(?string $code): bool
    {
        return in_array(Str::lower(trim((string) $code)), ['1', '0', '200', 'success', 'm0000'], true);
    }
}
