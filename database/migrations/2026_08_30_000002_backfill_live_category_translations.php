<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories') || ! Schema::hasTable('category_translations')) {
            return;
        }

        foreach ($this->categories() as $categoryData) {
            $categoryId = DB::table('categories')->where('id', $categoryData['id'])->value('id')
                ?? DB::table('categories')->whereIn('name', $categoryData['names'])->value('id');

            if (! $categoryId) {
                continue;
            }

            foreach ($categoryData['translations'] as $locale => $name) {
                $existing = DB::table('category_translations')
                    ->where('category_id', $categoryId)
                    ->where('locale', $locale)
                    ->first();

                if ($existing && filled($existing->name)) {
                    continue;
                }

                DB::table('category_translations')->updateOrInsert(
                    [
                        'category_id' => $categoryId,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $name,
                        'created_at' => $existing->created_at ?? now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        //
    }

    private function categories(): array
    {
        return [
            [
                'id' => 1,
                'names' => ['القضايا العمالية', 'قضايا عمالية'],
                'translations' => [
                    'ar-EG' => 'القضايا العمالية',
                    'en-US' => 'Labor Cases',
                    'fr-FR' => 'Affaires du travail',
                    'hi' => 'श्रम मामले',
                    'ur' => 'مزدوری کے معاملات',
                    'bn' => 'শ্রম মামলা',
                    'si' => 'කම්කරු නඩු',
                    'fil' => 'Mga Kaso sa Paggawa',
                    'ne' => 'श्रम मुद्दाहरू',
                    'id' => 'Kasus Ketenagakerjaan',
                ],
            ],
            [
                'id' => 2,
                'names' => ['الرواتب والمستحقات', 'قضايا الرواتب والأجور'],
                'translations' => [
                    'ar-EG' => 'الرواتب والمستحقات',
                    'en-US' => 'Salaries and Entitlements',
                    'fr-FR' => 'Salaires et droits',
                    'hi' => 'वेतन और देय अधिकार',
                    'ur' => 'تنخواہیں اور واجبات',
                    'bn' => 'বেতন ও প্রাপ্য',
                    'si' => 'වැටුප් සහ හිමිකම්',
                    'fil' => 'Mga Sahod at Karapatan',
                    'ne' => 'तलब र हकदाबी',
                    'id' => 'Gaji dan Hak',
                ],
            ],
            [
                'id' => 3,
                'names' => ['قضايا الفصل والتعويض'],
                'translations' => [
                    'ar-EG' => 'قضايا الفصل والتعويض',
                    'en-US' => 'Termination and Compensation Cases',
                    'fr-FR' => 'Affaires de licenciement et d’indemnisation',
                    'hi' => 'बर्खास्तगी और मुआवजा मामले',
                    'ur' => 'برطرفی اور معاوضے کے معاملات',
                    'bn' => 'চাকরিচ্যুতি ও ক্ষতিপূরণ মামলা',
                    'si' => 'සේවයෙන් ඉවත් කිරීම සහ වන්දි නඩු',
                    'fil' => 'Mga Kaso sa Pagtanggal at Kompensasyon',
                    'ne' => 'बर्खास्तगी र क्षतिपूर्ति मुद्दाहरू',
                    'id' => 'Kasus Pemutusan Kerja dan Kompensasi',
                ],
            ],
            [
                'id' => 4,
                'names' => ['قضايا عقد العمل', 'قضايا العقود'],
                'translations' => [
                    'ar-EG' => 'قضايا عقد العمل',
                    'en-US' => 'Employment Contract Cases',
                    'fr-FR' => 'Affaires de contrat de travail',
                    'hi' => 'रोजगार अनुबंध मामले',
                    'ur' => 'ملازمت کے معاہدے کے معاملات',
                    'bn' => 'কর্মসংস্থান চুক্তি মামলা',
                    'si' => 'රැකියා ගිවිසුම් නඩු',
                    'fil' => 'Mga Kaso sa Kontrata sa Trabaho',
                    'ne' => 'रोजगार करार मुद्दाहरू',
                    'id' => 'Kasus Kontrak Kerja',
                ],
            ],
            [
                'id' => 5,
                'names' => ['قضايا إصابات العمل'],
                'translations' => [
                    'ar-EG' => 'قضايا إصابات العمل',
                    'en-US' => 'Work Injury Cases',
                    'fr-FR' => 'Affaires d’accidents du travail',
                    'hi' => 'कार्यस्थल चोट मामले',
                    'ur' => 'کام کی چوٹ کے معاملات',
                    'bn' => 'কর্মক্ষেত্রের আঘাত মামলা',
                    'si' => 'රැකියා තුවාල නඩු',
                    'fil' => 'Mga Kaso sa Pinsala sa Trabaho',
                    'ne' => 'कार्यस्थल चोट मुद्दाहरू',
                    'id' => 'Kasus Cedera Kerja',
                ],
            ],
            [
                'id' => 6,
                'names' => ['قضايا التأمينات الاجتماعية ونهاية الخدمة', 'قضايا التأمينات الاجتماعية'],
                'translations' => [
                    'ar-EG' => 'قضايا التأمينات الاجتماعية ونهاية الخدمة',
                    'en-US' => 'Social Insurance and End-of-Service Cases',
                    'fr-FR' => 'Affaires d’assurance sociale et fin de service',
                    'hi' => 'सामाजिक बीमा और सेवा समाप्ति मामले',
                    'ur' => 'سوشل انشورنس اور اختتام خدمت کے معاملات',
                    'bn' => 'সামাজিক বীমা ও চাকরি সমাপ্তি মামলা',
                    'si' => 'සමාජ රක්ෂණ සහ සේවා අවසන් කිරීමේ නඩු',
                    'fil' => 'Mga Kaso sa Social Insurance at End of Service',
                    'ne' => 'सामाजिक बीमा र सेवा समाप्ति मुद्दाहरू',
                    'id' => 'Kasus Jaminan Sosial dan Akhir Masa Kerja',
                ],
            ],
            [
                'id' => 7,
                'names' => ['قضايا الإقامة ورخص العمل', 'قضايا الإقامة والعمل'],
                'translations' => [
                    'ar-EG' => 'قضايا الإقامة ورخص العمل',
                    'en-US' => 'Residency and Work Permit Cases',
                    'fr-FR' => 'Affaires de résidence et permis de travail',
                    'hi' => 'निवास और कार्य परमिट मामले',
                    'ur' => 'اقامہ اور ورک پرمٹ کے معاملات',
                    'bn' => 'আবাসন ও কাজের অনুমতি মামলা',
                    'si' => 'නේවාසික සහ වැඩ බලපත්‍ර නඩු',
                    'fil' => 'Mga Kaso sa Paninirahan at Work Permit',
                    'ne' => 'बसोबास र कार्य अनुमति मुद्दाहरू',
                    'id' => 'Kasus Izin Tinggal dan Izin Kerja',
                ],
            ],
            [
                'id' => 8,
                'names' => ['قضايا الشكاوي والمخالفات', 'قضايا المخالفات الإدارية'],
                'translations' => [
                    'ar-EG' => 'قضايا الشكاوي والمخالفات',
                    'en-US' => 'Complaints and Violations Cases',
                    'fr-FR' => 'Affaires de plaintes et infractions',
                    'hi' => 'शिकायत और उल्लंघन मामले',
                    'ur' => 'شکایات اور خلاف ورزیوں کے معاملات',
                    'bn' => 'অভিযোগ ও লঙ্ঘন মামলা',
                    'si' => 'පැමිණිලි සහ උල්ලංඝන නඩු',
                    'fil' => 'Mga Kaso sa Reklamo at Paglabag',
                    'ne' => 'उजुरी र उल्लङ्घन मुद्दाहरू',
                    'id' => 'Kasus Pengaduan dan Pelanggaran',
                ],
            ],
            [
                'id' => 9,
                'names' => ['قضايا الدوام والإجازات'],
                'translations' => [
                    'ar-EG' => 'قضايا الدوام والإجازات',
                    'en-US' => 'Working Hours and Leave Cases',
                    'fr-FR' => 'Affaires d’horaires de travail et congés',
                    'hi' => 'कार्य समय और अवकाश मामले',
                    'ur' => 'اوقات کار اور چھٹیوں کے معاملات',
                    'bn' => 'কাজের সময় ও ছুটি মামলা',
                    'si' => 'වැඩ කරන වේලාව සහ නිවාඩු නඩු',
                    'fil' => 'Mga Kaso sa Oras ng Trabaho at Leave',
                    'ne' => 'काम गर्ने समय र बिदा मुद्दाहरू',
                    'id' => 'Kasus Jam Kerja dan Cuti',
                ],
            ],
            [
                'id' => 10,
                'names' => ['استشارات أخرى'],
                'translations' => [
                    'ar-EG' => 'استشارات أخرى',
                    'en-US' => 'Other Consultations',
                    'fr-FR' => 'Autres consultations',
                    'hi' => 'अन्य परामर्श',
                    'ur' => 'دیگر مشاورتیں',
                    'bn' => 'অন্যান্য পরামর্শ',
                    'si' => 'වෙනත් උපදේශන',
                    'fil' => 'Iba Pang Konsultasyon',
                    'ne' => 'अन्य परामर्शहरू',
                    'id' => 'Konsultasi Lainnya',
                ],
            ],
        ];
    }
};
