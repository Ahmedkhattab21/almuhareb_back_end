<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Lawyer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LawyerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $lawyers = Lawyer::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get(['id']);

        $companies = Company::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get(['id']);

        $categories = Category::query()
            ->where('status', Category::STATUS_ACTIVE)
            ->orderBy('id')
            ->get(['id']);

        if ($lawyers->isEmpty() || $companies->isEmpty() || $categories->isEmpty()) {
            return;
        }

        DB::table('lawyers_categories')->truncate();

        $now = now();
        $rows = [];

        foreach ($companies as $companyIndex => $company) {
            $primaryLawyer = $lawyers[$companyIndex % $lawyers->count()];
            $secondaryLawyer = $lawyers[($companyIndex + 1) % $lawyers->count()];

            $firstCategorySet = $categories
                ->slice($companyIndex % max(1, $categories->count() - 1), 3)
                ->values();

            if ($firstCategorySet->count() < 3) {
                $firstCategorySet = $firstCategorySet
                    ->merge($categories->take(3 - $firstCategorySet->count()))
                    ->values();
            }

            $secondCategorySet = $categories
                ->slice(($companyIndex + 3) % max(1, $categories->count() - 1), 2)
                ->values();

            if ($secondCategorySet->count() < 2) {
                $secondCategorySet = $secondCategorySet
                    ->merge($categories->take(2 - $secondCategorySet->count()))
                    ->values();
            }

            foreach ($firstCategorySet as $category) {
                $rows[] = $this->assignmentRow($company->id, $primaryLawyer->id, $category->id, $now);
            }

            foreach ($secondCategorySet as $category) {
                $rows[] = $this->assignmentRow($company->id, $secondaryLawyer->id, $category->id, $now);
            }
        }

        DB::table('lawyers_categories')->insertOrIgnore($rows);
    }

    private function assignmentRow(int $companyId, int $lawyerId, int $categoryId, $timestamp): array
    {
        return [
            'company_id' => $companyId,
            'lawyer_id' => $lawyerId,
            'category_id' => $categoryId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}
