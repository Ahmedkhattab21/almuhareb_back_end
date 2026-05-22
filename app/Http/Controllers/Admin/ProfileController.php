<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profiles.show', [
            'layout' => 'layouts.app',
            'title' => 'الملف الشخصي',
            'subtitle' => 'عرض وتعديل بيانات حساب الأدمن.',
            'roleLabel' => 'مدير النظام',
            'user' => Auth::guard('admin')->user(),
            'updateRoute' => route('admin.profile.update'),
            'fields' => [
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'status' => 'الحالة',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admin', 'email')->ignore($admin->id)],
            'status' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return back()->with('toast_success', 'تم تحديث الملف الشخصي بنجاح.');
    }
}
