<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LawyerProfileController extends Controller
{
    public function show()
    {
        return view('profiles.show', [
            'layout' => 'layouts.lawyer',
            'title' => 'الملف الشخصي للمستشار',
            'subtitle' => 'عرض وتعديل بيانات حساب المستشار.',
            'roleLabel' => 'مستشار',
            'user' => Auth::guard('lawyer')->user(),
            'updateRoute' => route('lawyer.profile.update'),
            'fields' => [
                'name' => 'اسم المستشار',
                'email' => 'البريد الإلكتروني',
                'phone' => 'رقم الجوال',
                'preferred_language' => 'اللغة المفضلة',
                'status' => 'الحالة',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $lawyer = Auth::guard('lawyer')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('lawyers', 'email')->ignore($lawyer->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_language' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $lawyer->update($data);

        return back()->with('toast_success', 'تم تحديث الملف الشخصي بنجاح.');
    }
}
