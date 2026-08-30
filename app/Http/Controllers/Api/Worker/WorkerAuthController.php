<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use App\Models\WorkerLoginOtp;
use App\Services\Otp\OtpProviderManager;
use App\Services\Otp\PhoneNumberNormalizer;
use App\Services\WorkerLocalizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkerAuthController extends Controller
{
    public function __construct(
        private WorkerLocalizationService $localization,
        private PhoneNumberNormalizer $phoneNormalizer,
        private OtpProviderManager $otpProvider
    )
    {
    }

    public function requestCode(Request $request)
    {
        $this->localization->setLocale(null, $request);

        $validated = $request->validate([
            'phone' => ['required', 'string'],
        ], [
            'phone.required' => __('worker_auth.validation.phone_required'),
        ]);

        $msegatPhone = $this->normalizedPhoneOrFail($validated['phone']);
        $worker = $this->findWorkerByPhone($validated['phone']);
        $this->localization->setLocale($worker, $request);
        $ipThrottleKey = 'worker-login-otp-send:'.$request->ip();

        if (RateLimiter::tooManyAttempts($ipThrottleKey, 20)) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.too_many_sends'),
            ], 429);
        }

        if (! $worker) {
            throw ValidationException::withMessages([
                'phone' => [__('worker_auth.validation.phone_exists')],
            ]);
        }

        if (Schema::hasColumn('workers', 'status') && ($worker->status ?? 'active') !== 'active') {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.account_inactive'),
            ], 403);
        }

        $resendAfter = (int) config('services.otp.resend_after_seconds', 60);
        $expiresInMinutes = (int) config('services.otp.expires_in_minutes', 5);
        $maxSendsPerHour = (int) config('services.otp.max_sends_per_hour', 5);

        $latestOtp = WorkerLoginOtp::query()
            ->where('phone', $msegatPhone)
            ->latest()
            ->first();

        if ($latestOtp && $latestOtp->created_at->gt(now()->subSeconds($resendAfter))) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.resend_wait'),
                'data' => [
                    'resend_after' => max(1, $resendAfter - now()->diffInSeconds($latestOtp->created_at)),
                ],
            ], 429);
        }

        $sendsLastHour = WorkerLoginOtp::query()
            ->where('phone', $msegatPhone)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($sendsLastHour >= $maxSendsPerHour) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.too_many_sends'),
            ], 429);
        }

        $otpLanguage = $this->otpLanguage($request);
        RateLimiter::hit($ipThrottleKey, 3600);

        $sendResult = $this->otpProvider->sendOtp($msegatPhone, $otpLanguage);

        if (! $sendResult->successful || blank($sendResult->providerRequestId)) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.otp_provider_unavailable'),
            ], 503);
        }

        DB::transaction(function () use ($worker, $msegatPhone, $request, $otpLanguage, $sendResult, $expiresInMinutes) {
            WorkerLoginOtp::where('phone', $msegatPhone)
                ->where('status', 'pending')
                ->update([
                    'status' => 'invalidated',
                    'invalidated_at' => now(),
                    'used_at' => now(),
                ]);

            WorkerLoginOtp::create([
                'worker_id' => $worker->id,
                'phone' => $msegatPhone,
                'code_hash' => Hash::make($sendResult->providerRequestId),
                'provider' => $this->otpProvider->providerName(),
                'provider_request_id' => $sendResult->providerRequestId,
                'language' => $otpLanguage,
                'status' => 'pending',
                'attempts' => 0,
                'expires_at' => now()->addMinutes($expiresInMinutes),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'provider_code' => $sendResult->providerCode,
                    'original_phone' => $worker->phone,
                ],
            ]);
        });

        $response = [
            'status' => true,
            'message' => __('worker_auth.messages.code_sent'),
            'data' => [
                'masked_phone' => $this->phoneNormalizer->mask($msegatPhone),
                'resend_after' => $resendAfter,
                'expires_in' => $expiresInMinutes * 60,
            ],
        ];

        return response()->json($response);
    }

    public function verifyCode(Request $request)
    {
        $this->localization->setLocale(null, $request);

        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'digits_between:4,8'],
            'fcm_token' => ['nullable', 'string', 'max:4096'],
        ], [
            'phone.required' => __('worker_auth.validation.phone_required'),
            'code.required' => __('worker_auth.validation.code_required'),
            'code.digits_between' => __('worker_auth.validation.code_digits'),
        ]);

        $msegatPhone = $this->normalizedPhoneOrFail($validated['phone']);
        $worker = $this->findWorkerByPhone($validated['phone']);
        $this->localization->setLocale($worker, $request);

        if (! $worker) {
            throw ValidationException::withMessages([
                'phone' => [__('worker_auth.validation.phone_exists')],
            ]);
        }

        if (Schema::hasColumn('workers', 'status') && ($worker->status ?? 'active') !== 'active') {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.account_inactive'),
            ], 403);
        }

        $otp = WorkerLoginOtp::where('worker_id', $worker->id)
            ->where('phone', $msegatPhone)
            ->where('status', 'pending')
            ->whereNull('used_at')
            ->whereNull('verified_at')
            ->whereNull('invalidated_at')
            ->latest()
            ->first();

        if (! $otp) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.no_valid_code'),
            ], 422);
        }

        if ($otp->isExpired()) {
            $otp->update(['status' => 'expired']);

            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.code_expired'),
            ], 422);
        }

        $maxAttempts = (int) config('services.otp.max_verify_attempts', 5);

        if ($otp->attempts >= $maxAttempts) {
            $otp->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.too_many_attempts'),
            ], 429);
        }

        $verifyResult = $this->otpProvider->verifyOtp(
            (string) $otp->provider,
            (string) $otp->provider_request_id,
            Str::of((string) $validated['code'])->trim()->toString(),
            (string) $otp->language,
            $msegatPhone
        );

        if (! $verifyResult->successful) {
            $otp->increment('attempts');
            $otp->refresh();

            if ($otp->attempts >= $maxAttempts) {
                $otp->update(['status' => 'failed']);
            }

            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.invalid_code'),
            ], 422);
        }

        $otp->update([
            'status' => 'verified',
            'used_at' => now(),
            'verified_at' => now(),
        ]);

        $token = $worker->createToken('worker-mobile-token')->plainTextToken;

        if (! empty($validated['fcm_token']) && Schema::hasColumn('workers', 'fcm_token')) {
            $worker->forceFill([
                'fcm_token' => $validated['fcm_token'],
            ])->save();

            $worker->refresh();
        }

        return response()->json([
            'status' => true,
            'message' => __('worker_auth.messages.login_success'),
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'phone' => $worker->phone,
                    'company_id' => $worker->company_id,
                    'status' => $worker->status ?? 'active',
                    'fcm_token' => $worker->fcm_token ?? null,
                ],
            ],
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $this->localization->setLocale($request->user(), $request);

        $validated = $request->validate([
            'fcm_token' => ['nullable', 'string', 'max:4096'],
        ]);

        $worker = $request->user();

        if (Schema::hasColumn('workers', 'fcm_token')) {
            $worker->forceFill([
                'fcm_token' => $validated['fcm_token'] ?? null,
            ])->save();
        }

        return response()->json([
            'status' => true,
            'message' => __('worker_api.fcm_token_updated'),
            'data' => [
                'fcm_token' => $worker->fresh()->fcm_token ?? null,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $this->localization->setLocale($request->user(), $request);

        return response()->json([
            'status' => true,
            'message' => __('worker_auth.messages.profile_loaded'),
            'data' => [
                'worker' => $request->user(),
            ],
        ]);
    }

    public function preferredLanguages(Request $request)
    {
        $this->localization->setLocale($request->user(), $request);

        $languages = PreferedLanguage::query()
            ->where('status', 'active')
            ->orderBy('prefered_language')
            ->get(['id', 'prefered_language', 'code']);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.preferred_languages_fetched'),
            'data' => [
                'languages' => $languages,
            ],
        ]);
    }

    public function updatePreferredLanguage(Request $request)
    {
        $this->localization->setLocale($request->user(), $request);

        $validated = $request->validate([
            'preferred_language_id' => ['nullable', 'integer', 'exists:prefered_languages,id'],
            'preferred_language' => ['nullable', 'string', 'max:20'],
        ]);

        if (empty($validated['preferred_language_id']) && empty($validated['preferred_language'])) {
            return response()->json([
                'status' => false,
                'message' => __('worker_api.preferred_language_required'),
            ], 422);
        }

        $language = null;

        if (! empty($validated['preferred_language_id'])) {
            $language = PreferedLanguage::query()
                ->where('status', 'active')
                ->find($validated['preferred_language_id']);
        }

        if (! $language && ! empty($validated['preferred_language'])) {
            $language = PreferedLanguage::query()
                ->where('status', 'active')
                ->where('code', $validated['preferred_language'])
                ->first();
        }

        if (! $language) {
            return response()->json([
                'status' => false,
                'message' => __('worker_api.preferred_language_unavailable'),
            ], 422);
        }

        $worker = $request->user();
        $data = [];

        if (Schema::hasColumn('workers', 'preferred_language')) {
            $data['preferred_language'] = $language->code;
        }

        if (Schema::hasColumn('workers', 'prefered_language')) {
            $data['prefered_language'] = $language->code;
        }

        if (Schema::hasColumn('workers', 'language')) {
            $data['language'] = $language->code;
        }

        if (Schema::hasColumn('workers', 'preferred_language_id')) {
            $data['preferred_language_id'] = $language->id;
        }

        if (Schema::hasColumn('workers', 'prefered_language_id')) {
            $data['prefered_language_id'] = $language->id;
        }

        if (Schema::hasColumn('workers', 'language_id')) {
            $data['language_id'] = $language->id;
        }

        $worker->update($data);
        $worker = $worker->fresh();
        $this->localization->setLocale($worker, $request);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.preferred_language_updated'),
            'data' => [
                'worker' => $worker,
                'preferred_language' => [
                    'id' => $language->id,
                    'name' => $language->prefered_language,
                    'code' => $language->code,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $this->localization->setLocale($request->user(), $request);

        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => true,
            'message' => __('worker_auth.messages.logout_success'),
        ]);
    }

    private function findWorkerByPhone(string $phone): ?Worker
    {
        return Worker::query()
            ->whereIn('phone', $this->phoneNormalizer->lookupCandidates($phone))
            ->first();
    }

    private function normalizedPhoneOrFail(string $phone): string
    {
        $normalized = $this->phoneNormalizer->normalizeSaudi($phone);

        if (! $normalized) {
            throw ValidationException::withMessages([
                'phone' => [__('worker_auth.validation.phone_invalid')],
            ]);
        }

        return $normalized;
    }

    private function otpLanguage(Request $request): string
    {
        $locale = $request->input('lang')
            ?: $request->header('lang')
            ?: $request->header('X-Language')
            ?: $request->header('Accept-Language')
            ?: config('services.msegat.default_language', 'Ar');

        return str_starts_with(strtolower((string) $locale), 'ar') ? 'Ar' : 'En';
    }

}
