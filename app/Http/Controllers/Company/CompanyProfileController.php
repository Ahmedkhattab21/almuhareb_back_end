<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyProfileController extends Controller
{
    public function show()
    {
        return view('profiles.show', [
            'layout' => 'layouts.company',
            'title' => 'الملف الشخصي للشركة',
            'subtitle' => 'عرض وتعديل بيانات حساب الشركة.',
            'roleLabel' => 'بوابة الشركة',
            'user' => Auth::guard('company')->user(),
            'updateRoute' => route('company.profile.update'),
            'fields' => [
                'company_name' => 'اسم الشركة',
                'email' => 'البريد الإلكتروني',
                'phone' => 'رقم الجوال',
                'tax_number' => 'الرقم الضريبي',
                'address' => 'العنوان',
                'status' => 'الحالة',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $company = Auth::guard('company')->user();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('companies', 'email')->ignore($company->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $company->update($data);

        return back()->with('toast_success', 'تم تحديث الملف الشخصي بنجاح.');
    }
}
