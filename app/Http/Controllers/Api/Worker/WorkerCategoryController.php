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
            ->with('translations:category_id,locale,name')
            ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.company_id', $worker->company_id)
            ->where('categories.status', Category::STATUS_ACTIVE)
            ->distinct()
            ->orderBy('categories.name')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'translations' => $this->translationsFor($category),
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

    private function translationsFor(Category $category): array
    {
        $translations = $category->translations->pluck('name', 'locale')->all();

        foreach (['ar', 'en', 'fr', 'hi', 'ur', 'bn', 'si', 'fil', 'ne', 'id'] as $locale) {
            $translations[$locale] ??= $category->name;
        }

        return $translations;
    }
}
