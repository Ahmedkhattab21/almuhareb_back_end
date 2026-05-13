<?php

namespace Database\Seeders;

use App\Models\Nationality;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NationalitiesPreferedLanguageSeeder extends Seeder
{
    public function run()
    {
        if (
            ! Schema::hasTable('workers') ||
            ! Schema::hasTable('nationalities') ||
            ! Schema::hasTable('prefered_languages') ||
            ! Schema::hasTable('nationalities_prefered_language')
        ) {
            return;
        }

        $workers = Worker::query()->orderBy('id')->get();

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->get();

        $languages = PreferedLanguage::query()
            ->where('status', 'active')
            ->get();

        if ($workers->isEmpty() || $nationalities->isEmpty() || $languages->isEmpty()) {
            return;
        }

        foreach ($workers as $index => $worker) {
            $nationality = $nationalities[$index % $nationalities->count()];

            $language = $this->guessLanguageForNationality($nationality->nationality, $languages);

            DB::table('nationalities_prefered_language')->updateOrInsert(
                [
                    'worker_id' => $worker->id,
                ],
                [
                    'nationality_id' => $nationality->id,
                    'prefered_language_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function guessLanguageForNationality(string $nationality, $languages)
    {
        $languageCode = match (true) {
            str_contains($nationality, 'سعودي'),
            str_contains($nationality, 'مصري'),
            str_contains($nationality, 'سوداني'),
            str_contains($nationality, 'يمني'),
            str_contains($nationality, 'سوري'),
            str_contains($nationality, 'أردني'),
            str_contains($nationality, 'فلسطيني'),
            str_contains($nationality, 'لبناني'),
            str_contains($nationality, 'عراقي'),
            str_contains($nationality, 'مغربي'),
            str_contains($nationality, 'جزائري'),
            str_contains($nationality, 'تونسي') => 'ar',

            str_contains($nationality, 'باكستاني') => 'ur',

            str_contains($nationality, 'هندي') => 'hi',

            str_contains($nationality, 'بنجلاديشي') => 'bn',

            str_contains($nationality, 'فلبيني') => 'fil',

            str_contains($nationality, 'إندونيسي') => 'id',

            str_contains($nationality, 'نيبالي') => 'ne',

            str_contains($nationality, 'سريلانكي') => 'si',

            str_contains($nationality, 'إثيوبي') => 'am',

            default => 'en',
        };

        return $languages->firstWhere('code', $languageCode)
            ?? $languages->firstWhere('code', 'ar')
            ?? $languages->first();
    }
}
