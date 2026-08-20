<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategoryTranslationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->translations() as $categoryId => $translations) {
            $category = Category::query()->find($categoryId);

            if (! $category) {
                continue;
            }

            foreach ($translations as $locale => $name) {
                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $name,
                    ]
                );
            }
        }
    }

    private function translations(): array
    {
        return [
            1 => [
                'ar' => 'القضايا العمالية',
                'en' => 'Labor Cases',
                'fr' => 'Affaires du travail',
                'hi' => 'श्रम मामले',
                'ur' => 'مزدوری کے معاملات',
                'bn' => 'শ্রম মামলা',
                'si' => 'කම්කරු නඩු',
                'fil' => 'Mga Kaso sa Paggawa',
                'ne' => 'श्रम मुद्दाहरू',
                'id' => 'Kasus Ketenagakerjaan',
            ],
            2 => [
                'ar' => 'الرواتب والمستحقات',
                'en' => 'Salaries and Entitlements',
                'fr' => 'Salaires et droits',
                'hi' => 'वेतन और देय अधिकार',
                'ur' => 'تنخواہیں اور واجبات',
                'bn' => 'বেতন ও প্রাপ্য',
                'si' => 'වැටුප් සහ හිමිකම්',
                'fil' => 'Mga Sahod at Karapatan',
                'ne' => 'तलब र हकदाबी',
                'id' => 'Gaji dan Hak',
            ],
            3 => [
                'ar' => 'قضايا الفصل والتعويض',
                'en' => 'Termination and Compensation Cases',
                'fr' => 'Affaires de licenciement et d’indemnisation',
                'hi' => 'बर्खास्तगी और मुआवजा मामले',
                'ur' => 'برطرفی اور معاوضے کے معاملات',
                'bn' => 'চাকরিচ্যুতি ও ক্ষতিপূরণ মামলা',
                'si' => 'සේවයෙන් ඉවත් කිරීම සහ වන්දි නඩු',
                'fil' => 'Mga Kaso sa Pagtanggal at Kompensasyon',
                'ne' => 'बर्खास्तगी र क्षतिपूर्ति मुद्दाहरू',
                'id' => 'Kasus Pemutusan Kerja dan Kompensasi',
            ],
            4 => [
                'ar' => 'قضايا عقد العمل',
                'en' => 'Employment Contract Cases',
                'fr' => 'Affaires de contrat de travail',
                'hi' => 'रोजगार अनुबंध मामले',
                'ur' => 'ملازمت کے معاہدے کے معاملات',
                'bn' => 'কর্মসংস্থান চুক্তি মামলা',
                'si' => 'රැකියා ගිවිසුම් නඩු',
                'fil' => 'Mga Kaso sa Kontrata sa Trabaho',
                'ne' => 'रोजगार करार मुद्दाहरू',
                'id' => 'Kasus Kontrak Kerja',
            ],
            5 => [
                'ar' => 'قضايا إصابات العمل',
                'en' => 'Work Injury Cases',
                'fr' => 'Affaires d’accidents du travail',
                'hi' => 'कार्यस्थल चोट मामले',
                'ur' => 'کام کی چوٹ کے معاملات',
                'bn' => 'কর্মক্ষেত্রের আঘাত মামলা',
                'si' => 'රැකියා තුවාල නඩු',
                'fil' => 'Mga Kaso sa Pinsala sa Trabaho',
                'ne' => 'कार्यस्थल चोट मुद्दाहरू',
                'id' => 'Kasus Cedera Kerja',
            ],
            6 => [
                'ar' => 'قضايا التأمينات الاجتماعية ونهاية الخدمة',
                'en' => 'Social Insurance and End-of-Service Cases',
                'fr' => 'Affaires d’assurance sociale et fin de service',
                'hi' => 'सामाजिक बीमा और सेवा समाप्ति मामले',
                'ur' => 'سوشل انشورنس اور اختتام خدمت کے معاملات',
                'bn' => 'সামাজিক বীমা ও চাকরি সমাপ্তি মামলা',
                'si' => 'සමාජ රක්ෂණ සහ සේවා අවසන් කිරීමේ නඩු',
                'fil' => 'Mga Kaso sa Social Insurance at End of Service',
                'ne' => 'सामाजिक बीमा र सेवा समाप्ति मुद्दाहरू',
                'id' => 'Kasus Jaminan Sosial dan Akhir Masa Kerja',
            ],
            7 => [
                'ar' => 'قضايا الإقامة ورخص العمل',
                'en' => 'Residency and Work Permit Cases',
                'fr' => 'Affaires de résidence et permis de travail',
                'hi' => 'निवास और कार्य परमिट मामले',
                'ur' => 'اقامہ اور ورک پرمٹ کے معاملات',
                'bn' => 'আবাসন ও কাজের অনুমতি মামলা',
                'si' => 'නේවාසික සහ වැඩ බලපත්‍ර නඩු',
                'fil' => 'Mga Kaso sa Paninirahan at Work Permit',
                'ne' => 'बसोबास र कार्य अनुमति मुद्दाहरू',
                'id' => 'Kasus Izin Tinggal dan Izin Kerja',
            ],
            8 => [
                'ar' => 'قضايا الشكاوي والمخالفات',
                'en' => 'Complaints and Violations Cases',
                'fr' => 'Affaires de plaintes et infractions',
                'hi' => 'शिकायत और उल्लंघन मामले',
                'ur' => 'شکایات اور خلاف ورزیوں کے معاملات',
                'bn' => 'অভিযোগ ও লঙ্ঘন মামলা',
                'si' => 'පැමිණිලි සහ උල්ලංඝන නඩු',
                'fil' => 'Mga Kaso sa Reklamo at Paglabag',
                'ne' => 'उजुरी र उल्लङ्घन मुद्दाहरू',
                'id' => 'Kasus Pengaduan dan Pelanggaran',
            ],
            9 => [
                'ar' => 'قضايا الدوام والإجازات',
                'en' => 'Working Hours and Leave Cases',
                'fr' => 'Affaires d’horaires de travail et congés',
                'hi' => 'कार्य समय और अवकाश मामले',
                'ur' => 'اوقات کار اور چھٹیوں کے معاملات',
                'bn' => 'কাজের সময় ও ছুটি মামলা',
                'si' => 'වැඩ කරන වේලාව සහ නිවාඩු නඩු',
                'fil' => 'Mga Kaso sa Oras ng Trabaho at Leave',
                'ne' => 'काम गर्ने समय र बिदा मुद्दाहरू',
                'id' => 'Kasus Jam Kerja dan Cuti',
            ],
            10 => [
                'ar' => 'استشارات أخرى',
                'en' => 'Other Consultations',
                'fr' => 'Autres consultations',
                'hi' => 'अन्य परामर्श',
                'ur' => 'دیگر مشاورتیں',
                'bn' => 'অন্যান্য পরামর্শ',
                'si' => 'වෙනත් උපදේශන',
                'fil' => 'Iba Pang Konsultasyon',
                'ne' => 'अन्य परामर्शहरू',
                'id' => 'Konsultasi Lainnya',
            ],
        ];
    }
}
