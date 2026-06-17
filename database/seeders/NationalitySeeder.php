<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NationalitySeeder extends Seeder
{
    public function run()
    {
        $nationalitiesByLanguage = [
            'ar' => [
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
                'موريتاني',
            ],
            'en' => [
                'أمريكي',
                'بريطاني',
                'كندي',
                'أسترالي',
                'جنوب أفريقي',
                'نيجيري',
                'غاني',
                'كيني',
            ],
            'fr' => [
                'فرنسي',
                'سنغالي',
                'مالي',
                'إيفواري',
                'كاميروني',
            ],
            'hi' => [
                'هندي',
            ],
            'ur' => [
                'باكستاني',
            ],
            'bn' => [
                'بنجلاديشي',
            ],
            'si' => [
                'سيرلانكي',
            ],
            'fil' => [
                'فلبيني',
            ],
            'ne' => [
                'نيبالي',
            ],
            'id' => [
                'إندونيسي',
            ],
        ];

        $activeNationalities = collect($nationalitiesByLanguage)
            ->flatten()
            ->unique()
            ->values();

        foreach ($activeNationalities as $nationality) {
            Nationality::updateOrCreate(
                ['nationality' => $nationality],
                ['status' => 'active']
            );
        }

        $allowedIds = Nationality::whereIn('nationality', $activeNationalities->all())->pluck('id')->all();
        $fallbackNationalityId = Nationality::where('nationality', 'سعودي')->value('id');

        if ($fallbackNationalityId) {
            $this->moveOldNationalityReferences($allowedIds, $fallbackNationalityId);
        }

        Nationality::whereNotIn('nationality', $activeNationalities->all())->delete();
    }

    private function moveOldNationalityReferences(array $allowedIds, int $fallbackNationalityId): void
    {
        if (Schema::hasTable('nationalities_prefered_language')) {
            DB::table('nationalities_prefered_language')
                ->whereNotIn('nationality_id', $allowedIds)
                ->update([
                    'nationality_id' => $fallbackNationalityId,
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasTable('workers') || ! Schema::hasColumn('workers', 'nationality_id')) {
            return;
        }

        DB::table('workers')
            ->whereNotNull('nationality_id')
            ->whereNotIn('nationality_id', $allowedIds)
            ->update([
                'nationality_id' => $fallbackNationalityId,
                'updated_at' => now(),
            ]);
    }
}
