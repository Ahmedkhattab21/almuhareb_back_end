@php
    $category = $category ?? null;
    $isEdit = (bool) $category;
    $translationValues = collect($category?->translations ?? [])->pluck('name', 'locale');
    $languageOptions = $languageOptions ?? \App\Models\Category::supportedLanguageOptions();
    $completedTranslations = collect(\App\Models\Category::SUPPORTED_LOCALES)
        ->filter(fn ($locale) => filled(old('translations.' . $locale . '.name', $translationValues->get($locale, $locale === 'ar-EG' ? ($category->name ?? '') : ''))))
        ->count();
    $totalTranslations = count(\App\Models\Category::SUPPORTED_LOCALES);
@endphp

<section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
        <div class="flex items-center gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">1</span>
            <h2 class="text-lg font-black text-[#0f1b3d]">
                {{ $isEdit ? __('categories.edit.sections.basic') : __('categories.create.sections.basic') }}
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                {{ __('categories.form.name') }}
                <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', old('translations.ar-EG.name', $translationValues->get('ar-EG', $category->name ?? ''))) }}"
                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                placeholder="{{ __('categories.form.name_placeholder') }}">
            @error('name')
                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                {{ __('categories.form.status') }}
                <span class="text-red-500">*</span>
            </label>
            <select name="status" class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white">
                <option value="active" @selected(old('status', $category->status ?? 'active') === 'active')>{{ __('categories.status.active') }}</option>
                <option value="inactive" @selected(old('status', $category->status ?? 'active') === 'inactive')>{{ __('categories.status.inactive') }}</option>
            </select>
            @error('status')
                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

<section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">2</span>
            <div>
                <h2 class="text-lg font-black text-[#0f1b3d]">{{ __('categories.form.translations_title') }}</h2>
                <p class="mt-1 text-xs font-bold text-slate-400">{{ __('categories.form.translations_hint') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full {{ $completedTranslations === $totalTranslations ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }} px-4 py-2 text-xs font-black">
                {{ __('categories.form.translations_completed', ['completed' => $completedTranslations, 'total' => $totalTranslations]) }}
            </span>
            <div class="h-2 w-28 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full {{ $completedTranslations === $totalTranslations ? 'bg-green-500' : 'bg-amber-500' }}" style="width: {{ (int) (($completedTranslations / max($totalTranslations, 1)) * 100) }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
        @foreach($languageOptions as $locale => $language)
            @php
                $value = old('translations.' . $locale . '.name', $translationValues->get($locale, $locale === 'ar-EG' ? ($category->name ?? '') : ''));
                $isComplete = filled($value);
            @endphp
            <div>
                <label class="mb-2 flex items-center justify-between gap-2 text-sm font-extrabold text-[#0f1b3d]">
                    <span>
                        {{ __('categories.form.languages.' . $locale) }}
                        <span class="text-slate-400">({{ $language['native'] }})</span>
                        <span class="text-xs text-slate-400">{{ $locale }}</span>
                        <span class="text-red-500">*</span>
                    </span>
                    @if($isComplete)
                        <span class="rounded-full bg-green-50 px-2 py-1 text-[11px] font-black text-green-700">{{ __('categories.form.complete') }}</span>
                    @endif
                </label>
                <input type="text" name="translations[{{ $locale }}][name]" dir="{{ $language['direction'] }}"
                    value="{{ $value }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                    placeholder="{{ __('categories.form.translation_placeholder') }}">
                @error('translations.' . $locale . '.name')
                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>
</section>

<div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
            {{ $isEdit ? __('categories.edit.cancel') : __('categories.create.cancel') }}
        </a>
        <button type="submit" name="action" value="save_and_show" data-loading-button data-loading-text="{{ __('categories.loading.saving') }}"
            class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]">
            {{ $isEdit ? __('categories.edit.save_and_show') : __('categories.create.save_and_show') }}
        </button>
        <button type="submit" name="action" value="save" data-loading-button data-loading-text="{{ __('categories.loading.saving') }}"
            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
            {{ $isEdit ? __('categories.edit.save') : __('categories.create.save') }}
        </button>
    </div>
</div>
