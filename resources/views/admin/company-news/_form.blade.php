@php
    $isEdit = isset($newsItem);
@endphp

<div class="grid gap-5">
    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('company_news.form.company') }}</label>
        <select name="company_id"
            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]">
            <option value="">{{ __('company_news.form.select_company') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $newsItem->company_id ?? request('company_id')) == $company->id)>
                    {{ $company->company_name }}
                </option>
            @endforeach
        </select>
        @error('company_id') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('company_news.form.title') }}</label>
        <input type="text" name="title" value="{{ old('title', $newsItem->title ?? '') }}"
            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]"
            placeholder="{{ __('company_news.form.title_placeholder') }}">
        @error('title') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('company_news.form.image') }}</label>
        @if($isEdit && $newsItem->image_url)
            <img src="{{ $newsItem->image_url }}" alt="{{ $newsItem->title }}" class="mb-3 h-40 w-full rounded-2xl object-cover">
        @endif
        <input type="file" name="image" accept="image/*"
            class="w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-3 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]">
        @error('image') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('company_news.form.description') }}</label>
        <textarea name="description" rows="8"
            class="w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-3 text-sm font-bold leading-7 text-[#0f1b3d] outline-none focus:border-[#5368aa]"
            placeholder="{{ __('company_news.form.description_placeholder') }}">{{ old('description', $newsItem->description ?? '') }}</textarea>
        @error('description') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
