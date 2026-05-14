
@extends('layouts.company')

@section('title', __('company_positions.create.page_title'))

@section('content')
    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_positions.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('company.positions.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_positions.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_positions.create.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('company_positions.create.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('company_positions.create.subtitle') }}
                    </p>
                </div>

                <a href="{{ route('company.positions.index') }}"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                    {{ __('company_positions.create.back') }}
                </a>

            </div>
        </section>

        <form
            method="POST"
            action="{{ route('company.positions.store') }}"
            autocomplete="off"
            data-loading-form
            class="space-y-5"
        >
            @csrf

            <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                            1
                        </span>

                        <h2 class="text-lg font-black text-[#0f1b3d]">
                            {{ __('company_positions.create.sections.basic') }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                    {{-- Name --}}
                    <div>
                        <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                            {{ __('company_positions.form.name') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                            placeholder="{{ __('company_positions.form.name_placeholder') }}"
                        >

                        @error('name')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                            {{ __('company_positions.form.status') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="status"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                        >
                            <option value="active" @selected(old('status', 'active') === 'active')>
                                {{ __('company_positions.status.active') }}
                            </option>

                            <option value="inactive" @selected(old('status') === 'inactive')>
                                {{ __('company_positions.status.inactive') }}
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

        <a href="{{ route('company.positions.index') }}"
            class="inline-flex h-12 w-[145px] flex-none items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
            {{ __('company_positions.create.cancel') }}
        </a>

        <button
            type="submit"
            name="action"
            value="save_and_show"
            data-loading-button
            data-loading-id="company-position-create-save-show"
            data-loading-text="{{ __('company_positions.loading.saving') }}"
            class="inline-flex h-12 w-[145px] flex-none items-center justify-center rounded-2xl border border-[#5368aa] bg-white px-4 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]"
        >
            {{ __('company_positions.create.save_and_show') }}
        </button>

        <button
            type="submit"
            name="action"
            value="save"
            data-loading-button
            data-loading-id="company-position-create-save"
            data-loading-text="{{ __('company_positions.loading.saving') }}"
            class="inline-flex h-12 w-[145px] flex-none items-center justify-center rounded-2xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]"
        >
            {{ __('company_positions.create.save') }}
        </button>

    </div>
</div>

        </form>
    </div>
@endsection
