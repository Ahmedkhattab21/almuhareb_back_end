<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Nationality;
use App\Models\Position;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

            $phone = '05000000' . str_pad($number, 2, '0', STR_PAD_LEFT);

            $workerData = [
                'company_id' => $companyId,
                'created_by' => $companyId,

                'name' => $name,
                'email' => 'worker' . $number . '@example.com',
                'phone' => $phone,

                'iqama_number' => '20000000' . str_pad($number, 2, '0', STR_PAD_LEFT),

                'position_id' => $positionId,


                'status' => $index % 5 === 0 ? 'pending' : 'active',
            ];

            if (Schema::hasColumn('workers', 'image')) {
    $workerData['image'] = null;
}

      if (Schema::hasColumn('workers', 'nationality_id')) {
                $workerData['nationality_id'] = $nationalityId;
            }

            if (Schema::hasColumn('workers', 'prefered_language_id')) {
                $workerData['prefered_language_id'] = $languageId;
            }

            if (Schema::hasColumn('workers', 'preferred_language_id')) {
                $workerData['preferred_language_id'] = $languageId;
            }

            if (Schema::hasColumn('workers', 'language_id')) {
                $workerData['language_id'] = $languageId;
            }

            if (Schema::hasColumn('workers', 'open_tickets_count')) {
                $workerData['open_tickets_count'] = 0;
            }

            if (Schema::hasColumn('workers', 'tickets_count')) {
                $workerData['tickets_count'] = 0;
            }

            $worker = Worker::updateOrCreate(
                [
                    'phone' => $phone,
                ],
                $workerData
            );

            if (Schema::hasTable('nationalities_prefered_language')) {
                $pivotData = [
                    'nationality_id' => $nationalityId,
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('nationalities_prefered_language', 'prefered_language_id')) {
                    $pivotData['prefered_language_id'] = $languageId;
                }

                if (Schema::hasColumn('nationalities_prefered_language', 'preferred_language_id')) {
                    $pivotData['preferred_language_id'] = $languageId;
                }

                if (Schema::hasColumn('nationalities_prefered_language', 'language_id')) {
                    $pivotData['language_id'] = $languageId;
                }

                if (Schema::hasColumn('nationalities_prefered_language', 'created_at')) {
                    $pivotData['created_at'] = now();
                }

                DB::table('nationalities_prefered_language')->updateOrInsert(
                    [
                        'worker_id' => $worker->id,
                    ],
                    $pivotData
                );
            }
        }
    }
}
