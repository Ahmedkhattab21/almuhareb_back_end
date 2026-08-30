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
            ['prefered_language' => 'العربية', 'code' => 'ar-EG'],
            ['prefered_language' => 'الإنجليزية', 'code' => 'en-US'],
            ['prefered_language' => 'الفرنسية', 'code' => 'fr-FR'],
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
        $this->moveLegacyLanguageReferences([
            'ar' => 'ar-EG',
            'en' => 'en-US',
            'fr' => 'fr-FR',
        ]);

        $allowedIds = PreferedLanguage::whereIn('code', $allowedCodes)->pluck('id')->all();
        $fallbackLanguageId = PreferedLanguage::where('code', 'ar-EG')->value('id');

        if ($fallbackLanguageId) {
            $this->moveOldLanguageReferences($allowedIds, $fallbackLanguageId);
        }

        PreferedLanguage::whereNotIn('code', $allowedCodes)
            ->where('status', 'active')
            ->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);
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
                ->whereNotIn($column, ['ar-EG', 'en-US', 'fr-FR', 'hi', 'ur', 'bn', 'si', 'fil', 'ne', 'id'])
                ->update([
                    $column => 'ar-EG',
                    'updated_at' => now(),
                ]);
        }
    }

    private function moveLegacyLanguageReferences(array $codeMap): void
    {
        $idsByCode = PreferedLanguage::whereIn('code', array_merge(array_keys($codeMap), array_values($codeMap)))
            ->pluck('id', 'code');

        foreach ($codeMap as $oldCode => $newCode) {
            $oldId = $idsByCode[$oldCode] ?? null;
            $newId = $idsByCode[$newCode] ?? null;

            if (! $oldId || ! $newId) {
                continue;
            }

            if (Schema::hasTable('nationalities_prefered_language')) {
                DB::table('nationalities_prefered_language')
                    ->where('prefered_language_id', $oldId)
                    ->update([
                        'prefered_language_id' => $newId,
                        'updated_at' => now(),
                    ]);
            }

            if (! Schema::hasTable('workers')) {
                continue;
            }

            foreach (['prefered_language_id', 'preferred_language_id', 'language_id'] as $column) {
                if (! Schema::hasColumn('workers', $column)) {
                    continue;
                }

                DB::table('workers')
                    ->where($column, $oldId)
                    ->update([
                        $column => $newId,
                        'updated_at' => now(),
                    ]);
            }
        }

        if (! Schema::hasTable('workers')) {
            return;
        }

        foreach (['preferred_language', 'prefered_language', 'language'] as $column) {
            if (! Schema::hasColumn('workers', $column)) {
                continue;
            }

            foreach ($codeMap as $oldCode => $newCode) {
                DB::table('workers')
                    ->where($column, $oldCode)
                    ->update([
                        $column => $newCode,
                        'updated_at' => now(),
                    ]);
            }
        }
    }
}
