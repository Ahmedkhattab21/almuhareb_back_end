<?php

namespace Database\Seeders;

use App\Models\PreferedLanguage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PreferedLanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            ['prefered_language' => 'العربية', 'code' => 'ar'],
            ['prefered_language' => 'الإنجليزية', 'code' => 'en'],
            ['prefered_language' => 'الفرنسية', 'code' => 'fr'],
            ['prefered_language' => 'الهندية', 'code' => 'hi'],
            ['prefered_language' => 'الأوردو', 'code' => 'ur'],
            ['prefered_language' => 'البنغالية', 'code' => 'bn'],
            ['prefered_language' => 'السيرلانكية', 'code' => 'si'],
            ['prefered_language' => 'الفلبينية', 'code' => 'fil'],
            ['prefered_language' => 'النيبالية', 'code' => 'ne'],
            ['prefered_language' => 'الإندونيسية', 'code' => 'id'],
        ];

        foreach ($languages as $language) {
            PreferedLanguage::updateOrCreate(
                ['code' => $language['code']],
                [
                    'prefered_language' => $language['prefered_language'],
                    'status' => 'active',
                ]
            );
        }

        $allowedCodes = collect($languages)->pluck('code')->all();
        $allowedIds = PreferedLanguage::whereIn('code', $allowedCodes)->pluck('id')->all();
        $fallbackLanguageId = PreferedLanguage::where('code', 'ar')->value('id');

        if ($fallbackLanguageId) {
            $this->moveOldLanguageReferences($allowedIds, $fallbackLanguageId);
        }

        PreferedLanguage::whereNotIn('code', $allowedCodes)->delete();
    }

    private function moveOldLanguageReferences(array $allowedIds, int $fallbackLanguageId): void
    {
        if (Schema::hasTable('nationalities_prefered_language')) {
            DB::table('nationalities_prefered_language')
                ->whereNotIn('prefered_language_id', $allowedIds)
                ->update([
                    'prefered_language_id' => $fallbackLanguageId,
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasTable('workers')) {
            return;
        }

        foreach (['prefered_language_id', 'preferred_language_id', 'language_id'] as $column) {
            if (! Schema::hasColumn('workers', $column)) {
                continue;
            }

            DB::table('workers')
                ->whereNotNull($column)
                ->whereNotIn($column, $allowedIds)
                ->update([
                    $column => $fallbackLanguageId,
                    'updated_at' => now(),
                ]);
        }

        foreach (['preferred_language', 'prefered_language', 'language'] as $column) {
            if (! Schema::hasColumn('workers', $column)) {
                continue;
            }

            DB::table('workers')
                ->whereNotIn($column, ['ar', 'en', 'fr', 'hi', 'ur', 'bn', 'si', 'fil', 'ne', 'id'])
                ->update([
                    $column => 'ar',
                    'updated_at' => now(),
                ]);
        }
    }
}
