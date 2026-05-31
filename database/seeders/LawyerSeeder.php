<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Lawyer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class LawyerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();

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

            $admin = Admin::create($adminData);
        }

        $lawyers = [
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
        ];

        foreach ($lawyers as $index => $name) {
            $number = $index + 1;

            $data = [
                'admin_id' => $admin->id,
                'name' => $name,
                'email' => 'lawyer' . $number . '@almuharib.test',
                'phone' => '05000000' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'created_by' => $admin->id,
            ];

            if (Schema::hasColumn('lawyers', 'avatar')) {
                $data['avatar'] = null;
            }

            if (Schema::hasColumn('lawyers', 'preferred_language')) {
                $data['preferred_language'] = 'ar';
            }

            if (Schema::hasColumn('lawyers', 'rating')) {
                $data['rating'] = round(3.8 + (($index % 10) * 0.1), 1);
            }

            if (Schema::hasColumn('lawyers', 'active_cases_count')) {
                $data['active_cases_count'] = 0;
            }

            if (Schema::hasColumn('lawyers', 'avg_response_minutes')) {
                $data['avg_response_minutes'] = 45 + (($index * 9) % 120);
            }

            if (Schema::hasColumn('lawyers', 'resolution_rate')) {
                $data['resolution_rate'] = 80 + ($index % 15);
            }

            Lawyer::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
