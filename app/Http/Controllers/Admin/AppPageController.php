<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppPageController extends Controller
{
    public function index(Request $request)
    {
        $query = AppPage::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $pages = $query->paginate(10)->withQueryString();

        return view('admin.app-pages.index', compact('pages'));
    }

    public function create()
    {
        $types = $this->availableTypes();

        return view('admin.app-pages.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(AppPage::types()), Rule::unique('app_pages', 'type')],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $data['created_by_admin_id'] = auth('admin')->id();

        $page = AppPage::create($data);

        return redirect()
            ->route('admin.app-pages.show', $page)
            ->with('toast_success', __('app_pages.messages.created'));
    }

    public function show(AppPage $appPage)
    {
        $appPage->load('adminCreator:id,name,email');

        return view('admin.app-pages.show', compact('appPage'));
    }

    public function edit(AppPage $appPage)
    {
        return view('admin.app-pages.edit', compact('appPage'));
    }

    public function update(Request $request, AppPage $appPage)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $appPage->update($data);

        return redirect()
            ->route('admin.app-pages.show', $appPage)
            ->with('toast_success', __('app_pages.messages.updated'));
    }

    public function destroy(AppPage $appPage)
    {
        $appPage->delete();

        return redirect()
            ->route('admin.app-pages.index')
            ->with('toast_success', __('app_pages.messages.deleted'));
    }

    private function availableTypes(): array
    {
        $usedTypes = AppPage::query()->pluck('type')->all();

        return collect(AppPage::types())
            ->reject(fn (string $type) => in_array($type, $usedTypes, true))
            ->values()
            ->all();
    }
}
