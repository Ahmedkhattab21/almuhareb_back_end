<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();

        $companies = [
            ['name' => 'مجموعة سلك', 'city' => 'الرياض'],
            ['name' => 'ناديا للزراعة', 'city' => 'جدة'],
            ['name' => 'أرامكو السعودية', 'city' => 'الدمام'],
            ['name' => 'جدوى للاستثمار', 'city' => 'الرياض'],
            ['name' => 'شركة الخليج القانونية', 'city' => 'الخبر'],
            ['name' => 'شركة المدار للمقاولات', 'city' => 'مكة المكرمة'],
            ['name' => 'مؤسسة النخبة التجارية', 'city' => 'المدينة المنورة'],
            ['name' => 'شركة البيان للخدمات', 'city' => 'جدة'],
            ['name' => 'شركة روافد الأعمال', 'city' => 'الرياض'],
            ['name' => 'شركة المدى للتشغيل', 'city' => 'الدمام'],
            ['name' => 'شركة الرؤية الحديثة', 'city' => 'الرياض'],
            ['name' => 'شركة النور الصناعية', 'city' => 'الجبيل'],
        ];

        foreach ($companies as $index => $company) {
            $number = $index + 1;

            $data = [
                'created_by' => $admin?->id,
                'company_name' => $company['name'],
                'password' => Hash::make('password123'),
                'phone' => '05' . str_pad((string) (10000000 + $number), 8, '0', STR_PAD_LEFT),
                'tax_number' => (string) (3000000000 + $number),
                'address' => $company['city'],
                'status' => 'active',
            ];

            if (Schema::hasColumn('companies', 'lawyer_id')) {
                $data['lawyer_id'] = null;
            }

            Company::updateOrCreate(
                ['email' => 'company' . $number . '@example.com'],
                $data
            );
        }
    }
}
