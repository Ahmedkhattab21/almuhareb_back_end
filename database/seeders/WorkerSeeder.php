<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Nationality;
use App\Models\Position;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::query()
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $languages = PreferedLanguage::query()
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $positions = Position::query()
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        if (
            empty($companies) ||
            empty($nationalities) ||
            empty($languages) ||
            empty($positions)
        ) {
            return;
        }

        $names = [
            'أحمد الفارسي',
            'محمد خان',
            'سعيد علي',
            'رامي يوسف',
            'عبدالله حسن',
            'يوسف محمود',
            'خالد آدم',
            'مصطفى إبراهيم',
            'حسن عمر',
            'ناصر محمد',
            'علي منصور',
            'محمود سعيد',
            'حامد يوسف',
            'سليم حسن',
            'عبدالرحمن آدم',
            'كمال محمد',
            'نادر علي',
            'جابر حسين',
            'فهد سالم',
            'طارق إبراهيم',
        ];

        foreach ($names as $index => $name) {
            $number = $index + 1;

            $companyId = $companies[$index % count($companies)];
            $nationalityId = $nationalities[$index % count($nationalities)];
            $languageId = $languages[$index % count($languages)];
            $positionId = $positions[$index % count($positions)];

            $worker = Worker::updateOrCreate(
                [
                    'phone' => '05000000' . str_pad($number, 2, '0', STR_PAD_LEFT),
                ],
                [
                    'company_id' => $companyId,
                    'created_by' => $companyId,

                    'name' => $name,
                    'email' => 'worker' . $number . '@example.com',
                    'password' => Hash::make('12345678'),
                    'iqama_number' => '20000000' . str_pad($number, 2, '0', STR_PAD_LEFT),

                    'position_id' => $positionId,
                    'image' => null,
                    'status' => $index % 5 === 0 ? 'pending' : 'active',
                ]
            );

            DB::table('nationalities_prefered_language')->updateOrInsert(
                [
                    'worker_id' => $worker->id,
                ],
                [
                    'nationality_id' => $nationalityId,
                    'prefered_language_id' => $languageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
