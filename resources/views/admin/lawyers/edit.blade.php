@extends('layouts.app')

@section('title', __('lawyers.edit.page_title'))

@section('content')
    @php
        $status = old('status', $lawyer->status ?? 'active');

        $statusData = [
            'active' => [
                'label' => __('lawyers.status.active'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'pending' => [
                'label' => __('lawyers.status.pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
                'dot' => 'bg-yellow-500',
            ],
            'suspended' => [
                'label' => __('lawyers.status.suspended'),
                'class' => 'bg-red-50 text-red-700',
                'dot' => 'bg-red-500',
            ],
        ][$status] ?? [
            'label' => __('lawyers.status.unknown'),
            'class' => 'bg-slate-100 text-slate-600',
            'dot' => 'bg-slate-400',
        ];

        $lawyerName = $lawyer->name ?? '-';
        $avatarUrl = !empty($lawyer->avatar) ? asset('storage/' . $lawyer->avatar) : null;
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('lawyers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('admin.lawyers.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('lawyers.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('lawyers.edit.breadcrumb_current') }}
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
                                {{ __('lawyers.edit.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('lawyers.edit.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if (Route::has('admin.lawyers.show'))
                        <a href="{{ route('admin.lawyers.show', $lawyer->id) }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-6 text-sm font-extrabold text-blue-700 shadow-sm transition hover:bg-blue-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            {{ __('lawyers.edit.show_lawyer') }}
                        </a>
                    @endif

                    <a href="{{ route('admin.lawyers.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('lawyers.edit.back') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.lawyers.update', $lawyer->id) }}" enctype="multipart/form-data"
            autocomplete="off" data-loading-form class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_1fr]">
            @csrf
            @method('PUT')

            <input type="text" name="fake_username" autocomplete="username" class="hidden" tabindex="-1">
            <input type="password" name="fake_password" autocomplete="current-password" class="hidden" tabindex="-1">

            {{-- Side Info --}}
            <aside class="hidden xl:block">
                <div class="sticky top-6 space-y-5">

                    <div class="rounded-[26px] border border-slate-200 bg-white p-6 text-center shadow-sm">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $lawyerName }}"
                                class="mx-auto h-24 w-24 rounded-3xl border border-slate-200 object-cover shadow-sm">
                        @else
                            <div
                                class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-[#eef3ff] text-4xl font-black text-[#0f1b3d]">
                                {{ mb_substr($lawyerName, 0, 1) }}
                            </div>
                        @endif

                        <h3 class="mt-5 text-lg font-black text-[#0f1b3d]">
                            {{ $lawyerName }}
                        </h3>

                        <div class="mt-3 flex justify-center">
                            <span
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <p class="mt-3 text-xs font-bold text-slate-400">
                            {{ __('lawyers.edit.last_update') }}
                            {{ $lawyer->updated_at ? $lawyer->updated_at->format('Y-m-d H:i') : '-' }}
                        </p>
                    </div>

                    <div class="rounded-[26px] border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-yellow-100 text-yellow-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                            </span>

                            <h3 class="text-base font-black text-[#0f1b3d]">
                                {{ __('lawyers.edit.notes_title') }}
                            </h3>
                        </div>

                        <ul class="space-y-3 text-sm font-bold leading-7 text-slate-600">
                            <li>• {{ __('lawyers.edit.note_password') }}</li>
                            <li>• {{ __('lawyers.edit.note_status') }}</li>
                            <li>• {{ __('lawyers.edit.note_language') }}</li>
                        </ul>
                    </div>

                </div>
            </aside>

            {{-- Main --}}
            <div class="space-y-5">

                {{-- Profile Header Card --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="p-5">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-center gap-4">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $lawyerName }}"
                                        class="h-20 w-20 rounded-3xl border border-slate-200 object-cover shadow-sm">
                                @else
                                    <div
                                        class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-[#eef3ff] text-3xl font-black text-[#0f1b3d]">
                                        {{ mb_substr($lawyerName, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-xl font-black text-[#0f1b3d]">
                                            {{ $lawyerName }}
                                        </h2>

                                        <span
                                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                            {{ $statusData['label'] }}
                                        </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-4 text-sm font-bold text-slate-500">
                                        <span>{{ $lawyer->email ?? '-' }}</span>
                                        <span>{{ $lawyer->phone ?? '-' }}</span>
                                        <span>
                                            {{ __('lawyers.edit.created_at') }}:
                                            {{ $lawyer->created_at ? $lawyer->created_at->format('Y-m-d H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if (Route::has('admin.lawyers.show'))
                                <a href="{{ route('admin.lawyers.show', $lawyer->id) }}"
                                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-5 text-sm font-extrabold text-blue-700 transition hover:bg-blue-50">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    {{ __('lawyers.edit.show_lawyer') }}
                                </a>
                            @endif

                        </div>
                    </div>
                </section>

                {{-- Personal Data --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                1
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('lawyers.edit.sections.personal') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.name') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="name" value="{{ old('name', $lawyer->name) }}"
                                autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('lawyers.form.name_placeholder') }}">

                            @error('name')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.email') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="email" name="email" value="{{ old('email', $lawyer->email) }}"
                                autocomplete="new-email"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="lawyer@example.com">

                            @error('email')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.phone') }}
                            </label>

                            <input type="text" name="phone" value="{{ old('phone', $lawyer->phone) }}"
                                autocomplete="off" inputmode="tel"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="05xxxxxxxx">

                            @error('phone')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 xl:col-span-3">
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.avatar') }}
                            </label>

                            <label for="lawyerAvatarInput"
                                class="flex min-h-[160px] cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-[#f8fbff] px-4 py-6 text-center transition hover:border-[#5368aa] hover:bg-white">
                                <img id="lawyerAvatarPreview" src="{{ $avatarUrl ?? '' }}"
                                    alt="{{ __('lawyers.form.avatar') }}"
                                    class="{{ $avatarUrl ? '' : 'hidden' }} mb-4 h-24 w-24 rounded-full border border-slate-200 object-cover shadow-sm">

                                <div id="lawyerAvatarIcon" class="{{ $avatarUrl ? 'hidden' : '' }}">
                                    <svg class="h-9 w-9 text-[#5368aa]" fill="none" stroke="currentColor"
                                        stroke-width="2.2" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M17 8l-5-5-5 5" />
                                        <path d="M12 3v12" />
                                    </svg>
                                </div>

                                <span id="lawyerAvatarText" class="mt-3 text-sm font-black text-[#0f1b3d]">
                                    {{ $avatarUrl ? __('lawyers.form.avatar_change') : __('lawyers.form.avatar_upload') }}
                                </span>

                                <span id="lawyerAvatarFileName"
                                    class="mt-2 hidden text-xs font-extrabold text-green-600"></span>

                                <span class="mt-1 text-xs font-bold text-slate-400">
                                    JPG, PNG, WEBP - 2MB
                                </span>

                                <input id="lawyerAvatarInput" type="file" name="avatar" class="hidden"
                                    accept="image/*">
                            </label>

                            @error('avatar')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Platform Settings --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                2
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('lawyers.edit.sections.platform') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-4">

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.status') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="status"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white">
                                <option value="active" @selected(old('status', $lawyer->status) === 'active')>
                                    {{ __('lawyers.status.active') }}
                                </option>

                                <option value="pending" @selected(old('status', $lawyer->status) === 'pending')>
                                    {{ __('lawyers.status.pending') }}
                                </option>

                                <option value="suspended" @selected(old('status', $lawyer->status) === 'suspended')>
                                    {{ __('lawyers.status.suspended') }}
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('lawyers.table.cases_count') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                {{ number_format($lawyer->active_cases_count ?? 0) }}
                            </p>
                        </div>

                    </div>
                </section>

                {{-- Related Companies --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                3
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('lawyers.edit.sections.companies') }}
                            </h2>
                        </div>
                    </div>

                    <div class="space-y-4 p-5">
                        <p class="text-sm font-bold leading-7 text-slate-500">
                            {{ __('lawyers.edit.companies_hint') }}
                        </p>

                        @php
                            $selectedCompanies = old('company_ids', $selectedCompanyIds ?? []);
                            $selectedCompanies = is_array($selectedCompanies)
                                ? array_map('intval', $selectedCompanies)
                                : [];

                            $activeCompanies = collect($companies ?? [])->filter(function ($company) {
                                return ($company->status ?? 'active') === 'active';
                            });
                        @endphp

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @forelse($activeCompanies as $company)
                                <label
                                    class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:border-[#5368aa] hover:bg-white">
                                    <div>
                                        <p class="text-sm font-black text-[#0f1b3d]">
                                            {{ $company->company_name ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-slate-400">
                                            {{ $company->email ?? '' }}
                                        </p>
                                    </div>

                                    <input type="checkbox" name="company_ids[]" value="{{ $company->id }}"
                                        @checked(in_array((int) $company->id, $selectedCompanies, true))
                                        class="h-5 w-5 rounded border-slate-300 text-[#2563EB] focus:ring-[#2563EB]">
                                </label>
                            @empty
                                <div
                                    class="rounded-2xl border border-dashed border-slate-300 bg-[#f8fbff] p-6 text-center md:col-span-2 xl:col-span-3">
                                    <p class="text-sm font-bold text-slate-500">
                                        {{ __('lawyers.edit.no_active_companies') }}
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        @error('company_ids')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror

                        @error('company_ids.*')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- Account Settings --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                4
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('lawyers.edit.sections.account') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.new_password') }}
                            </label>

                            <input type="password" name="password" autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********">

                            <p class="mt-2 text-xs font-bold text-slate-400">
                                {{ __('lawyers.edit.password_hint') }}
                            </p>

                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('lawyers.form.password_confirmation') }}
                            </label>

                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********">

                            @error('password_confirmation')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Actions --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">

                        <a href="{{ route('admin.lawyers.index') }}"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>

                            <span>{{ __('lawyers.edit.cancel') }}</span>
                        </a>

                        <button type="submit" name="action" value="suspend" data-loading-button
                            data-loading-id="lawyer-edit-suspend" data-loading-text="{{ __('lawyers.loading.saving') }}"
                            onclick="return confirm('{{ __('lawyers.edit.confirm_suspend') }}')"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl bg-red-600 px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-red-700">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M4.93 4.93l14.14 14.14" />
                            </svg>

                            <span>{{ __('lawyers.edit.suspend_lawyer') }}</span>
                        </button>

                        @if (Route::has('admin.lawyers.show'))
                            <button type="submit" name="action" value="save_and_show" data-loading-button
                                data-loading-id="lawyer-edit-save-and-show"
                                data-loading-text="{{ __('lawyers.loading.saving') }}"
                                class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <path d="M19 12H5" />
                                    <path d="M12 19l-7-7 7-7" />
                                </svg>

                                <span>{{ __('lawyers.edit.save_and_show') }}</span>
                            </button>
                        @endif

                        <x-ui.button type="submit" name="action" value="save" :full="false" data-loading-button
                            data-loading-id="lawyer-edit-save" data-loading-text="{{ __('lawyers.loading.saving') }}"
                            class="!w-auto !min-w-[160px] !flex-none rounded-2xl px-6 text-sm font-extrabold">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>

                            <span>{{ __('lawyers.edit.save') }}</span>
                        </x-ui.button>

                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('lawyerAvatarInput');
            const preview = document.getElementById('lawyerAvatarPreview');
            const icon = document.getElementById('lawyerAvatarIcon');
            const text = document.getElementById('lawyerAvatarText');
            const fileName = document.getElementById('lawyerAvatarFileName');

            if (!input || !preview) {
                return;
            }

            input.addEventListener('change', function() {
                const file = input.files && input.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');

                    if (icon) {
                        icon.classList.add('hidden');
                    }

                    if (fileName) {
                        fileName.textContent = file.name;
                        fileName.classList.remove('hidden');
                    }

                    if (text) {
                        text.textContent = "{{ __('lawyers.form.avatar_uploaded') }}";
                    }
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
