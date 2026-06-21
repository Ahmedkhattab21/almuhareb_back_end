@extends('layouts.company')

@section('title', __('company_workers.edit.page_title'))

@section('content')
    @php
        $raw = $worker->getAttributes();

        $currentImage = $raw['image'] ?? $raw['avatar'] ?? null;

        $currentImageUrl = !empty($currentImage)
            ? asset('storage/' . $currentImage)
            : null;

        $currentNationalityId = old(
            'nationality_id',
            $selectedNationalityId
                ?? data_get($worker, 'nationalityPreferredLanguage.nationality_id')
                ?? ($raw['nationality_id'] ?? null)
        );

        $currentPreferedLanguageId = old(
            'prefered_language_id',
            $selectedPreferedLanguageId
                ?? data_get($worker, 'nationalityPreferredLanguage.prefered_language_id')
                ?? ($raw['prefered_language_id'] ?? null)
                ?? ($raw['preferred_language_id'] ?? null)
        );

        $currentPositionId = old(
            'position_id',
            $worker->position_id ?? ($raw['position_id'] ?? null)
        );

        $currentCityId = old(
            'city_id',
            $worker->city_id ?? ($raw['city_id'] ?? null)
        );

        $companyName = auth('company')->user()->company_name
            ?? auth('company')->user()->name
            ?? '-';

        $showUrl = Route::has('company.workers.show')
            ? route('company.workers.show', $worker->id)
            : route('company.workers.index');

        $indexUrl = Route::has('company.workers.index')
            ? route('company.workers.index')
            : url('/company/workers');
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_workers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ $indexUrl }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_workers.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_workers.edit.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('company_workers.edit.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('company_workers.edit.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if(Route::has('company.workers.show'))
                        <a href="{{ route('company.workers.show', $worker->id) }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-6 text-sm font-extrabold text-blue-700 shadow-sm transition hover:bg-blue-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            {{ __('company_workers.edit.show_worker') }}
                        </a>
                    @endif

                    <a href="{{ $indexUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('company_workers.edit.back') }}
                    </a>
                </div>

            </div>
        </section>

        <form
            method="POST"
            action="{{ route('company.workers.update', $worker->id) }}"
            enctype="multipart/form-data"
            autocomplete="off"
            data-loading-form
            class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_1fr]"
        >
            @csrf
            @method('PUT')

            {{-- Side Info --}}
            <aside class="hidden xl:block">
                <div class="sticky top-6 space-y-5">

                    <div class="rounded-[26px] border border-slate-200 bg-white p-6 text-center shadow-sm">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl bg-[#eef3ff] text-[#5368aa]">
                            @if($currentImageUrl)
                                <img src="{{ $currentImageUrl }}" alt="{{ $worker->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <span class="text-3xl font-black text-[#0f1b3d]">
                                    {{ mb_substr($worker->name ?? '-', 0, 1) }}
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-5 text-lg font-black text-[#0f1b3d]">
                            {{ $worker->name ?? '-' }}
                        </h3>

                        <p class="mt-2 text-sm font-bold text-slate-500">
                            {{ $companyName }}
                        </p>

                        <p class="mt-4 text-sm leading-7 text-slate-500">
                            {{ __('company_workers.edit.side_text') }}
                        </p>
                    </div>

                    <div class="rounded-[26px] border border-amber-200 bg-amber-50 p-5 shadow-sm">
                        <h3 class="text-sm font-black text-amber-700">
                            {{ __('company_workers.edit.notes_title') }}
                        </h3>

                        <ul class="mt-3 list-disc space-y-2 ps-5 text-sm font-bold leading-7 text-amber-700">
                            <li>{{ __('company_workers.edit.notes.image_optional') }}</li>
                            <li>{{ __('company_workers.edit.notes.company_fixed') }}</li>
                        </ul>
                    </div>

                </div>
            </aside>

            <div class="space-y-5">

                {{-- Notice --}}
                <div class="flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-blue-800">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                    </div>

                    <p class="text-sm font-bold leading-7">
                        {{ __('company_workers.edit.notice') }}
                    </p>
                </div>

                {{-- Personal Data --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                1
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('company_workers.edit.sections.personal') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                        {{-- Name --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.name') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $worker->name) }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('company_workers.form.name_placeholder') }}"
                            >

                            @error('name')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.phone') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', $worker->phone) }}"
                                inputmode="tel"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="05xxxxxxxx"
                            >

                            @error('phone')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.email') }}
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $worker->email) }}"
                                autocomplete="new-email"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="worker@example.com"
                            >

                            @error('email')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Iqama --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.iqama_number') }}
                            </label>

                            <input
                                type="text"
                                name="iqama_number"
                                value="{{ old('iqama_number', $worker->iqama_number ?? $worker->residency_number ?? $worker->national_id) }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('company_workers.form.iqama_placeholder') }}"
                            >

                            @error('iqama_number')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Operating Company --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.operating_company') }}
                            </label>

                            <input
                                type="text"
                                name="operating_company"
                                value="{{ old('operating_company', $worker->operating_company ?? ($raw['operating_company'] ?? null)) }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('company_workers.form.operating_company_placeholder') }}"
                            >

                            @error('operating_company')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nationality --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.nationality') }}
                            </label>

                            <select
                                name="nationality_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('company_workers.form.choose_nationality') }}
                                </option>

                                @foreach($nationalities ?? [] as $nationality)
                                    <option value="{{ $nationality->id }}" @selected((string) $currentNationalityId === (string) $nationality->id)>
                                        {{ $nationality->nationality ?? $nationality->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('nationality_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preferred Language --}}
                        {{-- City --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                المدينة
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="city_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">اختر المدينة</option>

                                @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}" @selected((string) $currentCityId === (string) $city->id)>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('city_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preferred Language --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.language') }}
                            </label>

                            <select
                                name="prefered_language_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('company_workers.form.choose_language') }}
                                </option>

                                @foreach($preferedLanguages ?? [] as $language)
                                    <option value="{{ $language->id }}" @selected((string) $currentPreferedLanguageId === (string) $language->id)>
                                        {{ $language->prefered_language ?? $language->preferred_language ?? $language->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('prefered_language_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="md:col-span-2 xl:col-span-3">
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.image') }}
                            </label>

                            <label
                                for="workerImageInput"
                                class="flex min-h-[170px] cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-[#f8fbff] px-4 py-6 text-center transition hover:border-[#5368aa] hover:bg-white"
                            >
                                <img
                                    id="workerImagePreview"
                                    src="{{ $currentImageUrl ?? '' }}"
                                    alt="{{ __('company_workers.form.image') }}"
                                    class="{{ $currentImageUrl ? '' : 'hidden' }} mb-4 h-24 w-24 rounded-full border border-slate-200 object-cover shadow-sm"
                                >

                                <div id="workerImageIcon" class="{{ $currentImageUrl ? 'hidden' : '' }}">
                                    <svg class="h-9 w-9 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M17 8l-5-5-5 5" />
                                        <path d="M12 3v12" />
                                    </svg>
                                </div>

                                <span id="workerImageText" class="mt-3 text-sm font-black text-[#0f1b3d]">
                                    {{ $currentImageUrl ? __('company_workers.form.image_change') : __('company_workers.form.image_upload') }}
                                </span>

                                <span id="workerImageFileName" class="mt-2 hidden text-xs font-extrabold text-green-600"></span>

                                <span class="mt-1 text-xs font-bold text-slate-400">
                                    JPG, PNG, WEBP - 2MB
                                </span>

                                <input id="workerImageInput" type="file" name="image" class="hidden" accept="image/*">
                            </label>

                            @error('image')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Work Data --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                2
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('company_workers.edit.sections.work') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        {{-- Company Readonly --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.company') }}
                            </label>

                            <input
                                type="text"
                                value="{{ $companyName }}"
                                readonly
                                class="h-12 w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-100 px-4 text-sm font-bold text-[#0f1b3d] outline-none"
                            >
                        </div>

                        {{-- Position --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.position') }}
                            </label>

                            <select
                                name="position_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('company_workers.form.choose_position') }}
                                </option>

                                @foreach($positions ?? [] as $position)
                                    <option value="{{ $position->id }}" @selected((string) $currentPositionId === (string) $position->id)>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('position_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('company_workers.form.status') }}
                            </label>

                            <select
                                name="status"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="active" @selected(old('status', $worker->status) === 'active')>
                                    {{ __('company_workers.status.active') }}
                                </option>

                                <option value="pending" @selected(old('status', $worker->status) === 'pending')>
                                    {{ __('company_workers.status.pending') }}
                                </option>

                                <option value="suspended" @selected(old('status', $worker->status) === 'suspended')>
                                    {{ __('company_workers.status.suspended') }}
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Actions --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-end gap-3">

                        <a href="{{ $showUrl }}"
                            class="inline-flex h-12 w-[145px] flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                            {{ __('company_workers.edit.cancel') }}
                        </a>

                        <button
                            type="submit"
                            name="action"
                            value="save"
                            data-loading-button
                            data-loading-id="company-worker-edit-save"
                            data-loading-text="{{ __('company_workers.loading.saving') }}"
                            class="inline-flex h-12 w-[145px] flex-none items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]"
                        >
                            {{ __('company_workers.edit.save') }}
                        </button>

                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('workerImageInput');
            const preview = document.getElementById('workerImagePreview');
            const icon = document.getElementById('workerImageIcon');
            const text = document.getElementById('workerImageText');
            const fileName = document.getElementById('workerImageFileName');

            if (!input || !preview) {
                return;
            }

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];

                if (!file) {
                    @if($currentImageUrl)
                        preview.src = "{{ $currentImageUrl }}";
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        text.textContent = "{{ __('company_workers.form.image_change') }}";
                    @else
                        preview.src = "";
                        preview.classList.add('hidden');
                        icon.classList.remove('hidden');
                        text.textContent = "{{ __('company_workers.form.image_upload') }}";
                    @endif

                    fileName.classList.add('hidden');
                    fileName.textContent = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');

                    fileName.textContent = file.name;
                    fileName.classList.remove('hidden');

                    text.textContent = "{{ __('company_workers.form.image_uploaded') }}";
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
