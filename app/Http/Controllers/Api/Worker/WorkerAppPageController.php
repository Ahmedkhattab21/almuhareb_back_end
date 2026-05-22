<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\AppPage;
use Illuminate\Http\JsonResponse;

class WorkerAppPageController extends Controller
{
    public function index(): JsonResponse
    {
        $pages = AppPage::query()
            ->latest()
            ->get()
            ->map(fn (AppPage $page) => $this->payload($page))
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'App pages fetched successfully.',
            'data' => [
                'pages' => $pages,
            ],
        ]);
    }

    public function show(string $type): JsonResponse
    {
        abort_if(! in_array($type, AppPage::types(), true), 404);

        $page = AppPage::query()
            ->where('type', $type)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'App page fetched successfully.',
            'data' => [
                'page' => $this->payload($page),
            ],
        ]);
    }

    private function payload(AppPage $page): array
    {
        return [
            'id' => $page->id,
            'type' => $page->type,
            'title' => $page->title,
            'content' => $page->content,
            'updated_at' => $page->updated_at?->toISOString(),
        ];
    }
}
