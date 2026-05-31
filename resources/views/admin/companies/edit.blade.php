@extends('layouts.app')

@section('title', __('companies.edit.page_title'))

@section('content')
    @php
        $status = old('status', $company->status ?? 'active');

        $statusData = [
            'active' => [
                'label' => __('companies.status.active'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'pending' => [
                'label' => __('companies.status.pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
                'dot' => 'bg-yellow-500',
            ],
            'suspended' => [
                'label' => __('companies.status.suspended'),
                'class' => 'bg-red-50 text-red-700',
                'dot' => 'bg-red-500',
            ],
        ][$status] ?? [
            'label' => __('companies.status.unknown'),
            'class' => 'bg-slate-100 text-slate-600',
            'dot' => 'bg-slate-400',
        ];

        $companyInitials = mb_substr($company->company_name ?? '-', 0, 2);
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                {{-- Title --}}
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('companies.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('admin.companies.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('companies.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('companies.edit.breadcrumb_current') }}
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
                                {{ __('companies.edit.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('companies.edit.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Back / Show Button --}}
                <div class="flex flex-wrap items-center gap-3">
                    @if (Route::has('admin.companies.show'))
                        <a href="{{ route('admin.companies.show', $company->id) }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-6 text-sm font-extrabold text-blue-700 shadow-sm transition hover:bg-blue-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            {{ __('companies.edit.show_company') }}
                        </a>
                    @endif

                    <a href="{{ route('admin.companies.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('companies.edit.back') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.companies.update', $company->id) }}" autocomplete="off"
            data-loading-form class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_1fr]">
            @csrf
            @method('PUT')

            {{-- Prevent Browser Autofill --}}
            <input type="text" name="fake_username" autocomplete="username" class="hidden" tabindex="-1">
            <input type="password" name="fake_password" autocomplete="current-password" class="hidden" tabindex="-1">

            {{-- Side Info --}}
            <aside class="hidden xl:block">
                <div class="sticky top-6 space-y-5">

                    {{-- Company Mini Card --}}
                    <div class="rounded-[26px] border border-slate-200 bg-white p-6 text-center shadow-sm">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#eef3ff] text-2xl font-black text-[#0f1b3d]">
                            {{ $companyInitials }}
                        </div>

                        <h3 class="mt-5 text-lg font-black text-[#0f1b3d]">
                            {{ $company->company_name }}
                        </h3>

                        <div class="mt-3 flex justify-center">
                            <span
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <p class="mt-3 text-xs font-bold text-slate-400">
                            {{ __('companies.edit.last_update') }}
                            {{ $company->updated_at?->format('Y-m-d H:i') ?? '-' }}
                        </p>
                    </div>

                    {{-- Important Notes --}}
                    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                            </span>

                            <h3 class="text-base font-black text-[#0f1b3d]">
                                {{ __('companies.edit.notes_title') }}
                            </h3>
                        </div>

                        <ul class="space-y-3 text-sm font-bold leading-7 text-slate-500">
                            <li>• {{ __('companies.edit.note_password') }}</li>
                            <li>• {{ __('companies.edit.note_suspend') }}</li>
                        </ul>
                    </div>

                </div>
            </aside>

            {{-- Main Content --}}
            <div class="space-y-5">

                {{-- Company Header Card --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="p-5">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-[#eef3ff] text-2xl font-black text-[#0f1b3d]">
                                    {{ $companyInitials }}
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-xl font-black text-[#0f1b3d]">
                                            {{ $company->company_name }}
                                        </h2>

                                        <span
                                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                            {{ $statusData['label'] }}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm font-bold text-slate-500">
                                        {{ __('companies.edit.last_update') }}
                                        {{ $company->updated_at?->format('Y-m-d H:i') ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            @if (Route::has('admin.companies.show'))
                                <a href="{{ route('admin.companies.show', $company->id) }}"
                                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-5 text-sm font-extrabold text-blue-700 transition hover:bg-blue-50">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    {{ __('companies.edit.show_company') }}
                                </a>
                            @endif

                        </div>
                    </div>
                </section>

                {{-- Company Basic Data --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                1
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('companies.edit.sections.basic') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                        {{-- Company Name --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.company_name') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="company_name"
                                value="{{ old('company_name', $company->company_name) }}" autocomplete="off"
                                autocapitalize="off" spellcheck="false"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('companies.form.company_name_placeholder') }}">

                            @error('company_name')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.email') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                autocomplete="new-email" autocapitalize="off" spellcheck="false"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="company@example.com">

                            @error('email')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.phone') }}
                            </label>

                            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                                autocomplete="off" inputmode="tel"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="05xxxxxxxx">

                            @error('phone')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tax Number --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.tax_number') }}
                            </label>

                            <input type="text" name="tax_number"
                                value="{{ old('tax_number', $company->tax_number) }}" autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('companies.form.tax_number_placeholder') }}">

                            @error('tax_number')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.status') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="status" autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white">
                                <option value="active" @selected(old('status', $company->status) === 'active')>
                                    {{ __('companies.status.active') }}
                                </option>

                                <option value="pending" @selected(old('status', $company->status) === 'pending')>
                                    {{ __('companies.status.pending') }}
                                </option>

                                <option value="suspended" @selected(old('status', $company->status) === 'suspended')>
                                    {{ __('companies.status.suspended') }}
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2 xl:col-span-3">
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.address') }}
                            </label>

                            <input type="text" name="address" value="{{ old('address', $company->address) }}"
                                autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('companies.form.address_placeholder') }}">

                            @error('address')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Account Settings --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                2
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('companies.edit.sections.account') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        {{-- Password --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.new_password') }}
                            </label>

                            <input type="password" name="password" autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********">

                            <p class="mt-2 text-xs font-bold text-slate-400">
                                {{ __('companies.edit.password_hint') }}
                            </p>

                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.password_confirmation') }}
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

                        <a href="{{ route('admin.companies.index') }}"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>

                            <span>{{ __('companies.edit.cancel') }}</span>
                        </a>

                        <button type="submit" name="action" value="suspend" data-loading-button
                            data-loading-id="edit-suspend" data-loading-text="{{ __('companies.loading.saving') }}"
                            onclick="return confirm('{{ __('companies.edit.confirm_suspend') }}')"
                            class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl bg-red-600 px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-red-700">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M4.93 4.93l14.14 14.14" />
                            </svg>

                            <span>{{ __('companies.edit.suspend_company') }}</span>
                        </button>

                        @if (Route::has('admin.companies.show'))
                            <button type="submit" name="action" value="save_and_show" data-loading-button
                                data-loading-id="edit-save-and-show"
                                data-loading-text="{{ __('companies.loading.saving') }}"
                                class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <path d="M19 12H5" />
                                    <path d="M12 19l-7-7 7-7" />
                                </svg>

                                <span>{{ __('companies.edit.save_and_show') }}</span>
                            </button>
                        @endif

                        <x-ui.button type="submit" name="action" value="save" :full="false" data-loading-button
                            data-loading-id="edit-save" data-loading-text="{{ __('companies.loading.saving') }}"
                            class="!w-auto !min-w-[160px] !flex-none rounded-2xl px-6 text-sm font-extrabold">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>

                            <span>{{ __('companies.edit.save') }}</span>
                        </x-ui.button>

                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection
