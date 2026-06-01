<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
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
            ])
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully.',
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }
}
