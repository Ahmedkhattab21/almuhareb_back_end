<?php

namespace Database\Seeders;

use App\Models\PreferedLanguage;
use Illuminate\Database\Seeder;

class PreferedLanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            [
                'prefered_language' => 'العربية',
                'code' => 'ar',
            ],
            [
                'prefered_language' => 'الإنجليزية',
                'code' => 'en',
            ],
            [
                'prefered_language' => 'الأوردو',
                'code' => 'ur',
            ],
            [
                'prefered_language' => 'الهندية',
                'code' => 'hi',
            ],
            [
                'prefered_language' => 'البنغالية',
                'code' => 'bn',
            ],
            [
                'prefered_language' => 'الفلبينية / التاغالوغ',
                'code' => 'fil',
            ],
            [
                'prefered_language' => 'الإندونيسية',
                'code' => 'id',
            ],
            [
                'prefered_language' => 'النيبالية',
                'code' => 'ne',
            ],
            [
                'prefered_language' => 'السنهالية',
                'code' => 'si',
            ],
            [
                'prefered_language' => 'التاميلية',
                'code' => 'ta',
            ],
            [
                'prefered_language' => 'المالايالامية',
                'code' => 'ml',
            ],
            [
                'prefered_language' => 'التيلوغوية',
                'code' => 'te',
            ],
            [
                'prefered_language' => 'الأمهرية',
                'code' => 'am',
            ],
            [
                'prefered_language' => 'السواحلية',
                'code' => 'sw',
            ],
            [
                'prefered_language' => 'الفرنسية',
                'code' => 'fr',
            ],
            [
                'prefered_language' => 'التركية',
                'code' => 'tr',
            ],
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
    }
}
