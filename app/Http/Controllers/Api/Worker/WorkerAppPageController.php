<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\AppPage;
use App\Services\WorkerLocalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerAppPageController extends Controller
{
    public function index(Request $request, WorkerLocalizationService $localization): JsonResponse
    {
        $pages = AppPage::query()
            ->latest()
            ->get()
            ->map(fn (AppPage $page) => $this->payload($page))
            ->values();

        return response()->json([
            'status' => true,
            'message' => $localization->api('app_pages_fetched', [], $request->user(), $request),
            'data' => [
                'pages' => $pages,
            ],
        ]);
    }

    public function show(Request $request, string $type, WorkerLocalizationService $localization): JsonResponse
    {
        abort_if(! in_array($type, AppPage::types(), true), 404);

        $page = AppPage::query()
            ->where('type', $type)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => $localization->api('app_page_fetched', [], $request->user(), $request),
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
