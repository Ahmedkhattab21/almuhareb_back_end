@php
    $category = $category ?? null;
    $isEdit = (bool) $category;
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
            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
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
