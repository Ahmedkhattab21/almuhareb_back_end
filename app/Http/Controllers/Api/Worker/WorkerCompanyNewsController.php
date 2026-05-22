<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\CompanyNews;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerCompanyNewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $worker = $request->user();
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $query = CompanyNews::query()
            ->with('company:id,company_name')
            ->where('company_id', $worker->company_id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Company news fetched successfully.',
            'data' => [
                'news' => collect($news->items())->map(fn (CompanyNews $item) => $this->payload($item))->values(),
                'pagination' => [
                    'current_page' => $news->currentPage(),
                    'last_page' => $news->lastPage(),
                    'per_page' => $news->perPage(),
                    'total' => $news->total(),
                ],
            ],
        ]);
    }

    public function show(Request $request, CompanyNews $companyNews): JsonResponse
    {
        abort_if((int) $companyNews->company_id !== (int) $request->user()->company_id, 403);

        $companyNews->load('company:id,company_name');

        return response()->json([
            'status' => true,
            'message' => 'Company news fetched successfully.',
            'data' => [
                'news' => $this->payload($companyNews),
            ],
        ]);
    }

    private function payload(CompanyNews $item): array
    {
        return [
            'id' => $item->id,
            'company_id' => $item->company_id,
            'company_name' => $item->company?->company_name,
            'title' => $item->title,
            'description' => $item->description,
            'image_url' => $item->image_url,
            'created_at' => $item->created_at?->toISOString(),
            'created_at_human' => $item->created_at?->diffForHumans(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }
}
