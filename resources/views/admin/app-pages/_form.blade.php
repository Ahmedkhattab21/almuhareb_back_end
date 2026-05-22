<div class="grid gap-5">
    @isset($types)
        <div>
            <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('app_pages.form.type') }}</label>
            <select name="type"
                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]">
                <option value="">{{ __('app_pages.form.select_type') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type') === $type)>
                        {{ __('app_pages.types.' . $type) }}
                    </option>
                @endforeach
            </select>
            @error('type') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
        </div>
    @endisset

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('app_pages.form.title') }}</label>
        <input type="text" name="title" value="{{ old('title', $appPage->title ?? '') }}"
            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none focus:border-[#5368aa]"
            placeholder="{{ __('app_pages.form.title_placeholder') }}">
        @error('title') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-600">{{ __('app_pages.form.content') }}</label>
        <textarea name="content" rows="14"
            class="w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-3 text-sm font-bold leading-7 text-[#0f1b3d] outline-none focus:border-[#5368aa]"
            placeholder="{{ __('app_pages.form.content_placeholder') }}">{{ old('content', $appPage->content ?? '') }}</textarea>
        @error('content') <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
