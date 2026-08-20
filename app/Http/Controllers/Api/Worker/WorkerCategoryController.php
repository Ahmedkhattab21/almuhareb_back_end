<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\WorkerLocalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerCategoryController extends Controller
{
    public function index(Request $request, WorkerLocalizationService $localization): JsonResponse
    {
        $worker = $request->user();

        $categories = Category::query()
            ->select('categories.id', 'categories.name')
            ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.company_id', $worker->company_id)
            ->where('categories.status', Category::STATUS_ACTIVE)
            ->distinct()
            ->orderBy('categories.name')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'translations' => $this->translationsFor($category->name),
            ])
            ->values();

        return response()->json([
            'status' => true,
            'message' => $localization->api('categories_fetched', [], $worker, $request),
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }

    private function translationsFor(string $name): array
    {
        $translations = [
            'قضايا عمالية' => [
                'ar' => 'قضايا عمالية',
                'en' => 'Labor Cases',
                'fr' => 'Affaires de travail',
                'hi' => 'श्रम मामले',
                'ur' => 'مزدوری کے معاملات',
                'bn' => 'শ্রম মামলা',
                'si' => 'කම්කරු නඩු',
                'fil' => 'Mga Kaso sa Paggawa',
                'ne' => 'श्रम मुद्दाहरू',
                'id' => 'Kasus Ketenagakerjaan',
            ],
            'قضايا الرواتب والأجور' => [
                'ar' => 'قضايا الرواتب والأجور',
                'en' => 'Salary and Wage Cases',
                'fr' => 'Affaires de salaires et rémunérations',
                'hi' => 'वेतन और मजदूरी मामले',
                'ur' => 'تنخواہ اور اجرت کے معاملات',
                'bn' => 'বেতন ও মজুরি মামলা',
                'si' => 'වැටුප් හා දීමනා නඩු',
                'fil' => 'Mga Kaso sa Sahod at Bayad',
                'ne' => 'तलब र ज्याला मुद्दाहरू',
                'id' => 'Kasus Gaji dan Upah',
            ],
            'قضايا الفصل والتعويض' => [
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
            'قضايا العقود' => [
                'ar' => 'قضايا العقود',
                'en' => 'Contract Cases',
                'fr' => 'Affaires de contrats',
                'hi' => 'अनुबंध मामले',
                'ur' => 'معاہدوں کے معاملات',
                'bn' => 'চুক্তি মামলা',
                'si' => 'ගිවිසුම් නඩු',
                'fil' => 'Mga Kaso sa Kontrata',
                'ne' => 'करार मुद्दाहरू',
                'id' => 'Kasus Kontrak',
            ],
            'قضايا إصابات العمل' => [
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
            'قضايا التأمينات الاجتماعية' => [
                'ar' => 'قضايا التأمينات الاجتماعية',
                'en' => 'Social Insurance Cases',
                'fr' => 'Affaires d’assurance sociale',
                'hi' => 'सामाजिक बीमा मामले',
                'ur' => 'سوشل انشورنس کے معاملات',
                'bn' => 'সামাজিক বীমা মামলা',
                'si' => 'සමාජ රක්ෂණ නඩු',
                'fil' => 'Mga Kaso sa Social Insurance',
                'ne' => 'सामाजिक बीमा मुद्दाहरू',
                'id' => 'Kasus Asuransi Sosial',
            ],
            'قضايا الإقامة والعمل' => [
                'ar' => 'قضايا الإقامة والعمل',
                'en' => 'Residency and Work Cases',
                'fr' => 'Affaires de résidence et de travail',
                'hi' => 'निवास और कार्य मामले',
                'ur' => 'اقامہ اور کام کے معاملات',
                'bn' => 'আবাসন ও কাজের মামলা',
                'si' => 'නේවාසික හා රැකියා නඩු',
                'fil' => 'Mga Kaso sa Paninirahan at Trabaho',
                'ne' => 'बसोबास र काम मुद्दाहरू',
                'id' => 'Kasus Izin Tinggal dan Kerja',
            ],
            'قضايا المخالفات الإدارية' => [
                'ar' => 'قضايا المخالفات الإدارية',
                'en' => 'Administrative Violation Cases',
                'fr' => 'Affaires d’infractions administratives',
                'hi' => 'प्रशासनिक उल्लंघन मामले',
                'ur' => 'انتظامی خلاف ورزیوں کے معاملات',
                'bn' => 'প্রশাসনিক লঙ্ঘন মামলা',
                'si' => 'පරිපාලන උල්ලංඝන නඩු',
                'fil' => 'Mga Kaso sa Administratibong Paglabag',
                'ne' => 'प्रशासनिक उल्लङ्घन मुद्दाहरू',
                'id' => 'Kasus Pelanggaran Administratif',
            ],
        ];

        return $translations[$name] ?? [
            'ar' => $name,
            'en' => $name,
            'fr' => $name,
            'hi' => $name,
            'ur' => $name,
            'bn' => $name,
            'si' => $name,
            'fil' => $name,
            'ne' => $name,
            'id' => $name,
        ];
    }
}
