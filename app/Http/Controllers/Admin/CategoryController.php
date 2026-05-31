<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()
            ->with('admin:id,name,email')
            ->withCount([
                'lawyers as lawyers_count' => fn ($q) => $q->select(DB::raw('count(distinct lawyers.id)')),
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'name_asc' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $categories = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('status', Category::STATUS_ACTIVE)->count(),
            'inactive' => Category::where('status', Category::STATUS_INACTIVE)->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $data['admin_id'] = auth('admin')->id();

            $category = Category::create($data);

            return redirect()
                ->route($request->input('action') === 'save_and_show' ? 'admin.categories.edit' : 'admin.categories.index', $request->input('action') === 'save_and_show' ? $category : [])
                ->with('toast_success', __('categories.messages.created'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('categories.messages.create_failed'));
        }
    }

    public function edit(Category $category)
    {
        $category->loadCount('lawyers');

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $category->update($data);

            return redirect()
                ->route($request->input('action') === 'save_and_show' ? 'admin.categories.edit' : 'admin.categories.index', $request->input('action') === 'save_and_show' ? $category : [])
                ->with('toast_success', __('categories.messages.updated'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('categories.messages.update_failed'));
        }
    }

    public function destroy(Category $category)
    {
        if ($category->lawyers()->exists()) {
            return back()->with('toast_error', __('categories.messages.delete_has_lawyers'));
        }

        try {
            $category->delete();

            return redirect()
                ->route('admin.categories.index')
                ->with('toast_success', __('categories.messages.deleted'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('toast_error', __('categories.messages.delete_failed'));
        }
    }
}
