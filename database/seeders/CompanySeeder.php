<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Lawyer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run(): void
    {
        $admin = Admin::first();

        $lawyerIds = Lawyer::query()->pluck('id')->toArray();

        $companies = [
            'مجموعة سلك',
            'ناديا للزراعة',
            'أرامكو السعودية',
            'جدوى للاستثمار',
            'شركة الخليج القانونية',
            'شركة المدار للمقاولات',
            'مؤسسة النخبة التجارية',
            'شركة البيان للخدمات',
            'شركة روافد الأعمال',
            'شركة المدى للتشغيل',
            'شركة الرؤية الحديثة',
            'شركة النور الصناعية',
            'شركة المسار الذكي',
            'شركة التميز للاستشارات',
            'شركة أفق الموارد',
            'شركة الأمان للخدمات',
            'شركة المتحدة للتقنية',
            'شركة البناء المتقدم',
            'شركة دار الخليج',
            'شركة الحلول المتكاملة',
            'شركة جسور الأعمال',
            'شركة الريادة المالية',
            'شركة وطن للخدمات',
            'شركة الصفوة للتجارة',
            'شركة الفارس للمقاولات',
            'شركة القمة للتشغيل',
            'شركة أساس الأعمال',
            'شركة المحور الإداري',
            'شركة السهم الذهبي',
            'شركة تمكين الموارد',
        ];

        $addresses = [
            'الرياض',
            'جدة',
            'الدمام',
            'مكة المكرمة',
            'المدينة المنورة',
        ];

        $statuses = [
            'active',
            'pending',
            'suspended',
        ];

        foreach ($companies as $index => $name) {
            Company::updateOrCreate(
                [
                    'email' => 'company' . ($index + 1) . '@example.com',
                ],
                [
                    'lawyer_id' => ! empty($lawyerIds)
                        ? $lawyerIds[$index % count($lawyerIds)]
                        : null,

                    'created_by' => $admin?->id,

                    'company_name' => $name,
                    'password' => Hash::make('password123'),
                    'phone' => '05' . rand(10000000, 99999999),
                    'tax_number' => (string) rand(1000000000, 9999999999),
                    'address' => $addresses[array_rand($addresses)],
                    'status' => $statuses[array_rand($statuses)],
                ]
            );
        }
    }
}
