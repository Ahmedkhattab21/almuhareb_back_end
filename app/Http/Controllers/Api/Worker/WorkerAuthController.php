<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use App\Models\WorkerLoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class WorkerAuthController extends Controller
{
    public function requestCode(Request $request)
    {
        $this->setLocaleFromHeader($request);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:workers,phone'],
        ], [
            'phone.required' => __('worker_auth.validation.phone_required'),
            'phone.exists' => __('worker_auth.validation.phone_exists'),
        ]);

        $worker = Worker::where('phone', $validated['phone'])->first();

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

        WorkerLoginOtp::where('worker_id', $worker->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Test Code
        |--------------------------------------------------------------------------
        | في local/testing الكود ثابت 1111.
        | بعد الربط مع شركة الاتصالات هنخلي الإنتاج random.
        */
        // $code = app()->environment(['local', 'testing'])
        //     ? '1111'
        //     : (string) random_int(1000, 9999);

                $code = '1111';

        WorkerLoginOtp::create([
            'worker_id' => $worker->id,
            'phone' => $worker->phone,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | SMS Integration Later
        |--------------------------------------------------------------------------
        | هنا بعدين هنربط شركة الاتصالات:
        |
        | SmsService::send(
        |     $worker->phone,
        |     __('worker_auth.sms.login_code', ['code' => $code])
        | );
        |
        */

        $response = [
            'status' => true,
            'message' => __('worker_auth.messages.code_sent'),
        ];

        // if (app()->environment(['local', 'testing'])) {
        //     $response['debug_code'] = $code;
        // }

        $response['debug_code'] = $code;

        return response()->json($response);
    }

    public function verifyCode(Request $request)
    {
        $this->setLocaleFromHeader($request);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:workers,phone'],
            'code' => ['required', 'digits:4'],
            'fcm_token' => ['nullable', 'string', 'max:4096'],
        ], [
            'phone.required' => __('worker_auth.validation.phone_required'),
            'phone.exists' => __('worker_auth.validation.phone_exists'),
            'code.required' => __('worker_auth.validation.code_required'),
            'code.digits' => __('worker_auth.validation.code_digits'),
        ]);

        $worker = Worker::where('phone', $validated['phone'])->first();

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
            ->where('phone', $worker->phone)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $otp) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.no_valid_code'),
            ], 422);
        }

        if ($otp->isExpired()) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.code_expired'),
            ], 422);
        }

        if ($otp->attempts >= 5) {
            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.too_many_attempts'),
            ], 429);
        }

        if (! Hash::check($validated['code'], $otp->code_hash)) {
            $otp->increment('attempts');

            return response()->json([
                'status' => false,
                'message' => __('worker_auth.messages.invalid_code'),
            ], 422);
        }

        $otp->update([
            'used_at' => now(),
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
        $this->setLocaleFromHeader($request);

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
            'message' => 'FCM token updated successfully.',
            'data' => [
                'fcm_token' => $worker->fresh()->fcm_token ?? null,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $this->setLocaleFromHeader($request);

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
        $this->setLocaleFromHeader($request);

        $languages = PreferedLanguage::query()
            ->where('status', 'active')
            ->orderBy('prefered_language')
            ->get(['id', 'prefered_language', 'code']);

        return response()->json([
            'status' => true,
            'message' => 'Preferred languages fetched successfully.',
            'data' => [
                'languages' => $languages,
            ],
        ]);
    }

    public function updatePreferredLanguage(Request $request)
    {
        $this->setLocaleFromHeader($request);

        $validated = $request->validate([
            'preferred_language_id' => ['nullable', 'integer', 'exists:prefered_languages,id'],
            'preferred_language' => ['nullable', 'string', 'max:20'],
        ]);

        if (empty($validated['preferred_language_id']) && empty($validated['preferred_language'])) {
            return response()->json([
                'status' => false,
                'message' => 'preferred_language_id or preferred_language is required.',
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
                'message' => 'Selected preferred language is not available.',
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

        return response()->json([
            'status' => true,
            'message' => 'Preferred language updated successfully.',
            'data' => [
                'worker' => $worker->fresh(),
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
        $this->setLocaleFromHeader($request);

        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => true,
            'message' => __('worker_auth.messages.logout_success'),
        ]);
    }

    private function setLocaleFromHeader(Request $request): void
    {
        $locale = $request->header('lang')
            ?? $request->header('X-Language')
            ?? $request->header('Accept-Language')
            ?? 'ar';

        $locale = strtolower(substr($locale, 0, 2));

        if (! in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        app()->setLocale($locale);
    }
}
