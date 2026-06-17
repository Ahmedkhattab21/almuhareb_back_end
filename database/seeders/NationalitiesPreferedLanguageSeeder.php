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
    private const TABLE = 'nationalities_prefered_language';

    public function run()
    {
        if (
            ! Schema::hasTable('workers') ||
            ! Schema::hasTable('nationalities') ||
            ! Schema::hasTable('prefered_languages') ||
            ! Schema::hasTable(self::TABLE)
        ) {
            return;
        }

        $languages = PreferedLanguage::query()
            ->where('status', 'active')
            ->get()
            ->keyBy('code');

        if ($languages->isEmpty()) {
            return;
        }

        $workers = Worker::query()
            ->with(['nationalityPreferredLanguage.nationality', 'nationality'])
            ->orderBy('id')
            ->get();

        foreach ($workers as $worker) {
            $nationality = $this->resolveWorkerNationality($worker);

            if (! $nationality) {
                continue;
            }

            $languageCode = $this->languageCodeForNationality($nationality->nationality);
            $language = $languages->get($languageCode) ?? $languages->get('ar') ?? $languages->first();

            DB::table(self::TABLE)->updateOrInsert(
                ['worker_id' => $worker->id],
                [
                    'nationality_id' => $nationality->id,
                    'prefered_language_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $this->syncWorkerColumns($worker, $nationality, $language);
        }
    }

    private function syncWorkerColumns(Worker $worker, Nationality $nationality, PreferedLanguage $language): void
    {
        $data = [];

        if (Schema::hasColumn('workers', 'nationality_id')) {
            $data['nationality_id'] = $nationality->id;
        }

        if (Schema::hasColumn('workers', 'prefered_language_id')) {
            $data['prefered_language_id'] = $language->id;
        }

        if (Schema::hasColumn('workers', 'preferred_language_id')) {
            $data['preferred_language_id'] = $language->id;
        }

        if (Schema::hasColumn('workers', 'language_id')) {
            $data['language_id'] = $language->id;
        }

        if (Schema::hasColumn('workers', 'preferred_language')) {
            $data['preferred_language'] = $language->code;
        }

        if (Schema::hasColumn('workers', 'prefered_language')) {
            $data['prefered_language'] = $language->code;
        }

        if (Schema::hasColumn('workers', 'language')) {
            $data['language'] = $language->code;
        }

        if ($data) {
            $worker->forceFill($data)->save();
        }
    }

    private function resolveWorkerNationality(Worker $worker): ?Nationality
    {
        if ($worker->nationalityPreferredLanguage?->nationality) {
            return $worker->nationalityPreferredLanguage->nationality;
        }

        if ($worker->nationality) {
            return $worker->nationality;
        }

        if ($worker->nationality_id) {
            return Nationality::find($worker->nationality_id);
        }

        return Nationality::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->first();
    }

    private function languageCodeForNationality(string $nationality): string
    {
        return match ($nationality) {
            'سعودي',
            'مصري',
            'سوداني',
            'يمني',
            'سوري',
            'أردني',
            'فلسطيني',
            'لبناني',
            'عراقي',
            'كويتي',
            'بحريني',
            'قطري',
            'إماراتي',
            'عماني',
            'مغربي',
            'جزائري',
            'تونسي',
            'ليبي',
            'موريتاني' => 'ar',

            'أمريكي',
            'بريطاني',
            'كندي',
            'أسترالي',
            'جنوب أفريقي',
            'نيجيري',
            'غاني',
            'كيني' => 'en',

            'فرنسي',
            'سنغالي',
            'مالي',
            'إيفواري',
            'كاميروني' => 'fr',

            'هندي' => 'hi',
            'باكستاني' => 'ur',
            'بنجلاديشي' => 'bn',
            'سيرلانكي' => 'si',
            'فلبيني' => 'fil',
            'نيبالي' => 'ne',
            'إندونيسي' => 'id',

            default => 'ar',
        };
    }
}
