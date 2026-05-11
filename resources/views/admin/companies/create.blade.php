@extends('layouts.app')

@section('title', __('companies.create.page_title'))

@section('content')
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
                            {{ __('companies.create.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4v18" />
                                <path d="M19 21V11l-6-4" />
                                <path d="M9 9h1" />
                                <path d="M9 13h1" />
                                <path d="M9 17h1" />
                                <path d="M14 13h1" />
                                <path d="M14 17h1" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('companies.create.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('companies.create.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Back Button --}}
                <div class="shrink-0">
                    <a href="{{ route('admin.companies.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('companies.create.back') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.companies.store') }}" autocomplete="off" data-loading-form
            class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_1fr]">
            @csrf

            {{-- Prevent Browser Autofill --}}
            <input type="text" name="fake_username" autocomplete="username" class="hidden" tabindex="-1">
            <input type="password" name="fake_password" autocomplete="current-password" class="hidden" tabindex="-1">

            {{-- Side Info --}}
            <aside class="hidden xl:block">
                <div class="sticky top-6 rounded-[26px] border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-black text-[#0f1b3d]">
                        {{ __('companies.create.side_title') }}
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        {{ __('companies.create.side_text') }}
                    </p>
                </div>
            </aside>

            {{-- Main Content --}}
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
                        {{ __('companies.create.notice') }}
                    </p>
                </div>

                {{-- Company Basic Data --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                1
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('companies.create.sections.basic') }}
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

                            <input type="text" name="company_name" value="{{ old('company_name', '') }}"
                                autocomplete="off" autocapitalize="off" spellcheck="false"
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

                            <input type="email" name="email" value="{{ old('email', '') }}" autocomplete="new-email"
                                autocapitalize="off" spellcheck="false"
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

                            <input type="text" name="phone" value="{{ old('phone', '') }}" autocomplete="off"
                                inputmode="tel"
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

                            <input type="text" name="tax_number" value="{{ old('tax_number', '') }}"
                                autocomplete="off"
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
                                <option value="active" @selected(old('status', 'active') === 'active')>
                                    {{ __('companies.status.active') }}
                                </option>

                                <option value="pending" @selected(old('status') === 'pending')>
                                    {{ __('companies.status.pending') }}
                                </option>

                                <option value="suspended" @selected(old('status') === 'suspended')>
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

                            <input type="text" name="address" value="{{ old('address', '') }}" autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="{{ __('companies.form.address_placeholder') }}">

                            @error('address')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- Legal Link --}}
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                                2
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('companies.create.sections.legal') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        {{-- Lawyer --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.lawyer') }}
                            </label>

                            <select name="lawyer_id" autocomplete="off"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white">
                                <option value="">
                                    {{ __('companies.form.choose_lawyer') }}
                                </option>

                                @foreach ($lawyers ?? [] as $lawyer)
                                    <option value="{{ $lawyer->id }}" @selected((string) old('lawyer_id') === (string) $lawyer->id)>
                                        {{ $lawyer->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('lawyer_id')
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
                                3
                            </span>

                            <h2 class="text-lg font-black text-[#0f1b3d]">
                                {{ __('companies.create.sections.account') }}
                            </h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        {{-- Password --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.password') }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="password" name="password" autocomplete="new-password"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                                placeholder="********">

                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                                {{ __('companies.form.password_confirmation') }}
                                <span class="text-red-500">*</span>
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
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>

                            {{ __('companies.create.cancel') }}
                        </a>

                        <button type="submit" name="action" value="save_and_add_worker" data-loading-button
                            data-loading-id="create-save-and-add-worker"
                            data-loading-text="{{ __('companies.loading.saving') }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v6" />
                                <path d="M16 11h6" />
                            </svg>

                            <span>{{ __('companies.create.save_and_add_worker') }}</span>
                        </button>

                        <x-ui.button type="submit" name="action" value="save" :full="false" data-loading-button
                            data-loading-id="create-save" data-loading-text="{{ __('companies.loading.saving') }}"
                            class="!w-auto !min-w-[160px] !flex-none rounded-2xl px-6 text-sm font-extrabold">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>

                            <span>{{ __('companies.create.save') }}</span>
                        </x-ui.button>

                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection
