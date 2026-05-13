@extends('layouts.app')

@section('title', __('workers.create.page_title'))

@section('content')
    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('workers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('admin.workers.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('workers.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('workers.create.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v6" />
                                <path d="M16 11h6" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('workers.create.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('workers.create.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('admin.workers.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('workers.create.back') }}
                    </a>
                </div>

            </div>
        </section>

        <form
            method="POST"
            action="{{ route('admin.workers.store') }}"
            enctype="multipart/form-data"
            autocomplete="off"
            data-loading-form
            class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_1fr]"
        >
            @csrf

            <input type="text" name="fake_username" autocomplete="username" class="hidden" tabindex="-1">
            <input type="password" name="fake_password" autocomplete="current-password" class="hidden" tabindex="-1">

            {{-- Side Info --}}
            <aside class="hidden xl:block">
                <div class="sticky top-6 rounded-[26px] border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-[#0f1b3d]">
                        {{ __('workers.create.side_title') }}
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        {{ __('workers.create.side_text') }}
                    </p>
                </div>
            </aside>

            <div class="space-y-5">

                {{-- Notice --}}
                <div class="flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-blue-800">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                    </div>

                    <p class="text-sm font-bold leading-7">
                        {{ __('workers.create.notice') }}
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
                                {{ __('workers.create.sections.personal') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                        {{-- Name --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.name') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', '') }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('workers.form.name_placeholder') }}"
                            >

                            @error('name')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.phone') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', '') }}"
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
                                {{ __('workers.form.email') }}
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', '') }}"
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
                                {{ __('workers.form.iqama_number') }}
                            </label>

                            <input
                                type="text"
                                name="iqama_number"
                                value="{{ old('iqama_number', '') }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('workers.form.iqama_placeholder') }}"
                            >

                            @error('iqama_number')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nationality --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.nationality') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="nationality_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('workers.form.choose_nationality') }}
                                </option>

                                @foreach($nationalities ?? [] as $nationality)
                                    <option value="{{ $nationality->id }}" @selected((string) old('nationality_id') === (string) $nationality->id)>
                                        {{ $nationality->nationality }}
                                    </option>
                                @endforeach
                            </select>

                            @error('nationality_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prefered Language --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.prefered_language') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="prefered_language_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('workers.form.choose_prefered_language') }}
                                </option>

                                @foreach($preferedLanguages ?? [] as $language)
                                    <option value="{{ $language->id }}" @selected((string) old('prefered_language_id') === (string) $language->id)>
                                        {{ $language->prefered_language }}
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
                                {{ __('workers.form.image') }}
                            </label>

                            <label
                                for="workerImageInput"
                                class="flex min-h-[160px] cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-[#f8fbff] px-4 py-6 text-center transition hover:border-[#5368aa] hover:bg-white"
                            >
                                <img
                                    id="workerImagePreview"
                                    src=""
                                    alt="{{ __('workers.form.image') }}"
                                    class="mb-4 hidden h-24 w-24 rounded-full border border-slate-200 object-cover shadow-sm"
                                >

                                <div id="workerImageIcon">
                                    <svg class="h-9 w-9 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M17 8l-5-5-5 5" />
                                        <path d="M12 3v12" />
                                    </svg>
                                </div>

                                <span id="workerImageText" class="mt-3 text-sm font-black text-[#0f1b3d]">
                                    {{ __('workers.form.image_upload') }}
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
                                {{ __('workers.create.sections.work') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        {{-- Company --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.company') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="company_id"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="">
                                    {{ __('workers.form.choose_company') }}
                                </option>

                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('company_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    {{-- Position --}}
<div>
    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
        {{ __('workers.form.position') }}
        <span class="text-red-500">*</span>
    </label>

    <select
        name="position_id"
        class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
    >
        <option value="">
            {{ __('workers.form.choose_position') }}
        </option>

        @foreach($positions ?? [] as $position)
            <option value="{{ $position->id }}" @selected((string) old('position_id') === (string) $position->id)>
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
                                {{ __('workers.form.status') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="status"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                            >
                                <option value="active" @selected(old('status', 'active') === 'active')>
                                    {{ __('workers.status.active') }}
                                </option>

                                <option value="pending" @selected(old('status') === 'pending')>
                                    {{ __('workers.status.pending') }}
                                </option>

                                <option value="suspended" @selected(old('status') === 'suspended')>
                                    {{ __('workers.status.suspended') }}
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Account Settings --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                3
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('workers.create.sections.account') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.password') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********"
                            >

                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('workers.form.password_confirmation') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********"
                            >

                            @error('password_confirmation')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Actions --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">

                        <a href="{{ route('admin.workers.index') }}"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>

                            <span>{{ __('workers.create.cancel') }}</span>
                        </a>

                        <button
                            type="submit"
                            name="action"
                            value="save_and_add_another"
                            data-loading-button
                            data-loading-id="worker-create-save-and-add"
                            data-loading-text="{{ __('workers.loading.saving') }}"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v6" />
                                <path d="M16 11h6" />
                            </svg>

                            <span>{{ __('workers.create.save_and_add_another') }}</span>
                        </button>

                        <x-ui.button
                            type="submit"
                            name="action"
                            value="save"
                            :full="false"
                            data-loading-button
                            data-loading-id="worker-create-save"
                            data-loading-text="{{ __('workers.loading.saving') }}"
                            class="!w-auto !min-w-[160px] !flex-none rounded-2xl px-6 text-sm font-extrabold"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>

                            <span>{{ __('workers.create.save') }}</span>
                        </x-ui.button>

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
                    preview.classList.add('hidden');
                    icon.classList.remove('hidden');
                    fileName.classList.add('hidden');
                    fileName.textContent = '';
                    text.textContent = "{{ __('workers.form.image_upload') }}";
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');

                    fileName.textContent = file.name;
                    fileName.classList.remove('hidden');

                    text.textContent = "{{ __('workers.form.image_uploaded') }}";
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
