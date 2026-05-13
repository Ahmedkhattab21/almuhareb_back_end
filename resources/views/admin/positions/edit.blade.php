@extends('layouts.app')

@section('title', __('positions.edit.page_title'))

@section('content')
    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('positions.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('admin.positions.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('positions.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('positions.edit.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('positions.edit.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('positions.edit.subtitle') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.positions.show', $position->id) }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-6 text-sm font-extrabold text-blue-700 shadow-sm transition hover:bg-blue-50">
                        {{ __('positions.edit.show_position') }}
                    </a>

                    <a href="{{ route('admin.positions.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        {{ __('positions.edit.back') }}
                    </a>
                </div>

            </div>
        </section>

        <form
            method="POST"
            action="{{ route('admin.positions.update', $position->id) }}"
            autocomplete="off"
            data-loading-form
            class="space-y-5"
        >
            @csrf
            @method('PUT')

            <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                            1
                        </span>

                        <h2 class="text-lg font-black text-[#0f1b3d]">
                            {{ __('positions.edit.sections.basic') }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                    {{-- Name --}}
                    <div>
                        <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                            {{ __('positions.form.name') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $position->name) }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                            placeholder="{{ __('positions.form.name_placeholder') }}"
                        >

                        @error('name')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">
                            {{ __('positions.form.status') }}
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="status"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-[#0f1b3d] outline-none transition focus:border-[#5368aa] focus:bg-white"
                        >
                            <option value="active" @selected(old('status', $position->status) === 'active')>
                                {{ __('positions.status.active') }}
                            </option>

                            <option value="inactive" @selected(old('status', $position->status) === 'inactive')>
                                {{ __('positions.status.inactive') }}
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
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">

                    <a href="{{ route('admin.positions.show', $position->id) }}"
                        class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                        {{ __('positions.edit.cancel') }}
                    </a>

                    <button
                        type="submit"
                        name="action"
                        value="save_and_show"
                        data-loading-button
                        data-loading-id="position-edit-save-show"
                        data-loading-text="{{ __('positions.loading.saving') }}"
                        class="inline-flex h-12 w-auto flex-none items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]"
                    >
                        {{ __('positions.edit.save_and_show') }}
                    </button>

                    <x-ui.button
                        type="submit"
                        name="action"
                        value="save"
                        :full="false"
                        data-loading-button
                        data-loading-id="position-edit-save"
                        data-loading-text="{{ __('positions.loading.saving') }}"
                        class="!w-auto !min-w-[160px] !flex-none rounded-2xl px-6 text-sm font-extrabold"
                    >
                        <span>{{ __('positions.edit.save') }}</span>
                    </x-ui.button>

                </div>
            </div>

        </form>
    </div>
@endsection
