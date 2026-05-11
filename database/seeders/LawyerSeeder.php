<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Lawyer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class LawyerSeeder extends Seeder
{

  public function run()
    {
        $admin = Admin::first();

        if (! $admin) {
            $adminData = [
                'name' => 'أدمن المحارب',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'),
                 'status' => 'active',
            ];

            if (Schema::hasColumn('admin', 'admin_type')) {
                $adminData['admin_type'] = 'main_admin';
            }

            if (Schema::hasColumn('admin', 'status')) {
                $adminData['status'] = 'active';
            }

            $admin = Admin::create($adminData);
        }

        $names = [
            'خالد منصور',
            'ليلى رشيد',
            'عمر فاروق',
            'سارة العتيبي',
            'أحمد الشريف',
            'نورة القحطاني',
            'محمد العتيبي',
            'ريم الحربي',
            'عبدالله الزهراني',
            'هند الدوسري',
            'فيصل الغامدي',
            'منى السبيعي',
            'تركي المالكي',
            'دلال المطيري',
            'ناصر العنزي',
            'جود الشمري',
            'بدر الشهراني',
            'شهد القحطاني',
            'ماجد الفارس',
            'أمل اليامي',
            'راكان الحربي',
            'غادة السالم',
            'يوسف الدوسري',
            'بيان العتيبي',
            'سلمان المطيري',
            'رنا الحربي',
            'إبراهيم القحطاني',
            'لطيفة الغامدي',
            'عبدالرحمن المالكي',
            'دانة الفهد',
        ];

        $specializations = [
            'قانون العمل',
            'قانون الشركات',
            'قانون العقود',
            'القضايا العمالية',
            'الاستشارات القانونية',
            'الامتثال والحوكمة',
            'التحكيم التجاري',
            'القانون الإداري',
        ];

        $statuses = [
            'active',
            'active',
            'active',
            'pending',
            'suspended',
        ];

        foreach ($names as $index => $name) {
            $number = $index + 1;

            $data = [
                'admin_id' => $admin->id,
                'name' => $name,
                'email' => 'lawyer' . $number . '@almuharib.test',
                'phone' => '05000000' . str_pad($number, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('12345678'),
                'status' => $statuses[$index % count($statuses)],
                'preferred_language' => 'ar',
                'created_by' => $admin->id,
            ];

            if (Schema::hasColumn('lawyers', 'license_number')) {
                $data['license_number'] = 'LAW-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }

            if (Schema::hasColumn('lawyers', 'specialization')) {
                $data['specialization'] = $specializations[$index % count($specializations)];
            }

            if (Schema::hasColumn('lawyers', 'avatar')) {
                $data['avatar'] = null;
            }

            if (Schema::hasColumn('lawyers', 'rating')) {
                // تقييم من 5
                $data['rating'] = round(3.5 + (($index % 15) * 0.1), 1);
            }

            if (Schema::hasColumn('lawyers', 'active_cases_count')) {
                $data['active_cases_count'] = 5 + (($index * 3) % 45);
            }

            if (Schema::hasColumn('lawyers', 'avg_response_minutes')) {
                $data['avg_response_minutes'] = 30 + (($index * 17) % 240);
            }

            if (Schema::hasColumn('lawyers', 'resolution_rate')) {
                $data['resolution_rate'] = 70 + (($index * 2) % 29);
            }

            Lawyer::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }


}
