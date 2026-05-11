<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();

        if (Company::count() === 0) {
            Company::create([
                'company_name' => 'شركة تجريبية',
                'email' => 'company@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '0500000000',
                'tax_number' => '1234567890',
                'address' => 'الرياض',
                'status' => 'active',
                'created_by' => $admin?->id,
            ]);
        }

        $companyIds = Company::query()->pluck('id')->toArray();
        $nationalityIds = Nationality::query()->pluck('id')->toArray();

        $names = [
            'أحمد الفارسي',
            'راجيندرا كومار',
            'مصطفى علي',
            'ليام بارك',
            'محمد حسين',
            'عبدالله خالد',
            'سعيد عمر',
            'خالد منصور',
            'إبراهيم حسن',
            'حسن علي',
            'علي محمود',
            'يوسف ناصر',
            'فهد سالم',
            'راشد عبدالله',
            'نايف محمد',
            'عمر صديق',
            'كريم أحمد',
            'بلال مصطفى',
            'هاني سامي',
            'رامي جمال',
            'سلمان يوسف',
            'طارق إبراهيم',
            'مازن فهد',
            'عبدالرحمن صالح',
            'محمود مرعي',
            'أشرف سيد',
            'وليد حسن',
            'سامي علي',
            'نادر كمال',
            'أيمن لطفي',
        ];

        $positions = [
            'عامل',
            'سائق',
            'فني صيانة',
            'عامل نظافة',
            'مشرف موقع',
            'عامل مخزن',
            'مندوب',
        ];

        $statuses = [
            'active',
            'pending',
            'suspended',
        ];

        foreach ($names as $index => $name) {
            Worker::updateOrCreate(
                [
                    'phone' => '050'.str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                ],
                [
                    'company_id' => $companyIds[array_rand($companyIds)],
                    'created_by' => $admin?->id,
                    'name' => $name,
                    'email' => 'worker'.($index + 1).'@example.com',
                    'password' => Hash::make('password123'),
                    'iqama_number' => (string) (2000000000 + $index),
                    'nationality_id' => ! empty($nationalityIds)
                        ? $nationalityIds[array_rand($nationalityIds)]
                        : null,
                    'position' => $positions[array_rand($positions)],
                    'image' => null,
                    'status' => $statuses[array_rand($statuses)],
                ]
            );
        }
    }
}
