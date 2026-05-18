<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
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
        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:workers,phone'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.exists' => 'رقم الهاتف غير مسجل لدينا.',
        ]);

        $worker = Worker::where('phone', $validated['phone'])->first();

        if (! $worker) {
            throw ValidationException::withMessages([
                'phone' => ['رقم الهاتف غير مسجل لدينا.'],
            ]);
        }

        if (Schema::hasColumn('workers', 'status') && ($worker->status ?? 'active') !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'هذا الحساب غير نشط حالياً. يرجى التواصل مع الإدارة.',
            ], 403);
        }

        WorkerLoginOtp::where('worker_id', $worker->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        $code = (string) random_int(100000, 999999);

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
        | هنا هتربط شركة الرسائل SMS
        |--------------------------------------------------------------------------
        | مثال:
        | SmsService::send($worker->phone, "كود تسجيل الدخول الخاص بك هو: {$code}");
        |
        | مؤقتاً في local هنرجع الكود في response للتجربة.
        */

        $response = [
            'status' => true,
            'message' => 'تم إرسال كود التحقق إلى رقم الهاتف.',
        ];

        if (app()->environment('local')) {
            $response['debug_code'] = $code;
        }

        return response()->json($response);
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:workers,phone'],
            'code' => ['required', 'digits:6'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.exists' => 'رقم الهاتف غير مسجل لدينا.',
            'code.required' => 'كود التحقق مطلوب.',
            'code.digits' => 'كود التحقق يجب أن يكون 6 أرقام.',
        ]);

        $worker = Worker::where('phone', $validated['phone'])->first();

        if (! $worker) {
            throw ValidationException::withMessages([
                'phone' => ['رقم الهاتف غير مسجل لدينا.'],
            ]);
        }

        if (Schema::hasColumn('workers', 'status') && ($worker->status ?? 'active') !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'هذا الحساب غير نشط حالياً. يرجى التواصل مع الإدارة.',
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
                'message' => 'لا يوجد كود تحقق صالح. يرجى طلب كود جديد.',
            ], 422);
        }

        if ($otp->isExpired()) {
            return response()->json([
                'status' => false,
                'message' => 'انتهت صلاحية كود التحقق. يرجى طلب كود جديد.',
            ], 422);
        }

        if ($otp->attempts >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'تم تجاوز عدد المحاولات المسموح. يرجى طلب كود جديد.',
            ], 429);
        }

        if (! Hash::check($validated['code'], $otp->code_hash)) {
            $otp->increment('attempts');

            return response()->json([
                'status' => false,
                'message' => 'كود التحقق غير صحيح.',
            ], 422);
        }

        $otp->update([
            'used_at' => now(),
        ]);

        $token = $worker->createToken('worker-mobile-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
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
                ],
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات العامل بنجاح.',
            'data' => [
                'worker' => $request->user(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }
}
