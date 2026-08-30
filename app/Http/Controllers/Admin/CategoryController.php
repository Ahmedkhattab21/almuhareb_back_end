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
        $locale = Category::normalizeLocale(app()->getLocale());

        $query = Category::query()
            ->with('translations:category_id,locale,name')
            ->with('admin:id,name,email')
            ->withCount([
                'lawyers as lawyers_count' => fn ($q) => $q->select(DB::raw('count(distinct lawyers.id)')),
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($translationQuery) use ($search) {
                        $translationQuery->where('name', 'like', "%{$search}%");
                    });
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

        return view('admin.categories.index', compact('categories', 'stats', 'locale'));
    }

    public function create()
    {
        $languageOptions = Category::supportedLanguageOptions();

        return view('admin.categories.create', compact('languageOptions'));
    }

    public function store(Request $request)
    {
        $translations = $this->normalizeTranslationsInput($request->input('translations', []));
        $request->merge([
            'translations' => $translations,
            'name' => data_get($translations, 'ar-EG.name', $request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'status' => ['required', 'in:active,inactive'],
            'translations' => ['required', 'array'],
        ] + $this->translationValidationRules(), $this->translationValidationMessages());

        try {
            $data['admin_id'] = auth('admin')->id();

            $category = DB::transaction(function () use ($data) {
                $arabicName = $data['translations']['ar-EG']['name'] ?? $data['name'];

                $category = Category::create([
                    'admin_id' => $data['admin_id'],
                    'name' => $arabicName,
                    'status' => $data['status'],
                ]);

                $this->syncTranslations($category, $data['translations'] ?? []);

                return $category;
            });

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
        $category->load(['translations'])->loadCount('lawyers');
        $languageOptions = Category::supportedLanguageOptions();

        return view('admin.categories.edit', compact('category', 'languageOptions'));
    }

    public function update(Request $request, Category $category)
    {
        $translations = $this->normalizeTranslationsInput($request->input('translations', []));
        $request->merge([
            'translations' => $translations,
            'name' => data_get($translations, 'ar-EG.name', $request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'status' => ['required', 'in:active,inactive'],
            'translations' => ['required', 'array'],
        ] + $this->translationValidationRules(), $this->translationValidationMessages());

        try {
            DB::transaction(function () use ($category, $data) {
                $arabicName = $data['translations']['ar-EG']['name'] ?? $data['name'];

                $category->update([
                    'name' => $arabicName,
                    'status' => $data['status'],
                ]);

                $this->syncTranslations($category, $data['translations'] ?? []);
            });

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

    private function syncTranslations(Category $category, array $translations): void
    {
        foreach ($this->supportedLocales() as $locale) {
            $name = trim((string) data_get($translations, "{$locale}.name", ''));

            if ($name === '') {
                continue;
            }

            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }
    }

    private function supportedLocales(): array
    {
        return Category::SUPPORTED_LOCALES;
    }

    private function translationValidationRules(): array
    {
        return collect($this->supportedLocales())
            ->mapWithKeys(fn (string $locale) => [
                "translations.{$locale}.name" => ['required', 'string', 'max:255'],
            ])
            ->all();
    }

    private function translationValidationMessages(): array
    {
        return collect($this->supportedLocales())
            ->flatMap(fn (string $locale) => [
                "translations.{$locale}.name.required" => __('categories.validation.translation_required', [
                    'language' => __('categories.form.languages.'.$locale),
                ]),
                "translations.{$locale}.name.max" => __('categories.validation.translation_max', [
                    'language' => __('categories.form.languages.'.$locale),
                    'max' => 255,
                ]),
            ])
            ->all();
    }

    private function normalizeTranslationsInput(array $translations): array
    {
        return collect($translations)
            ->mapWithKeys(function ($value, $locale) {
                $locale = Category::normalizeLocale($locale);
                $name = is_array($value) ? ($value['name'] ?? null) : $value;

                return [$locale => ['name' => is_string($name) ? trim($name) : $name]];
            })
            ->only($this->supportedLocales())
            ->all();
    }
}
