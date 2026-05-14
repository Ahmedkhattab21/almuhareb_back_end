@extends('layouts.company')

@section('title', __('company_positions.show.page_title'))

@section('content')
    @php
        $status = $position->status ?? 'active';

        $statusData = [
            'active' => [
                'label' => __('company_positions.status.active'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'inactive' => [
                'label' => __('company_positions.status.inactive'),
                'class' => 'bg-red-50 text-red-700',
                'dot' => 'bg-red-500',
            ],
        ][$status] ?? [
            'label' => __('company_positions.status.unknown'),
            'class' => 'bg-slate-100 text-slate-600',
            'dot' => 'bg-slate-400',
        ];

        $indexUrl = Route::has('company.positions.index') ? route('company.positions.index') : '#';
        $editUrl = Route::has('company.positions.edit') ? route('company.positions.edit', $position->id) : '#';
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_positions.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ $indexUrl }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_positions.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_positions.show.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('company_positions.show.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('company_positions.show.subtitle') }}
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $indexUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        {{ __('company_positions.show.back') }}
                    </a>

                    <a href="{{ $editUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#16264f]">
                        {{ __('company_positions.actions.edit') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Main Card --}}
        <section class="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-[1fr_260px] lg:items-center">

                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-3xl font-black text-[#0f1b3d]">
                            {{ $position->name }}
                        </h2>

                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('company_positions.table.id') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                #{{ $position->id }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('company_positions.show.workers_count') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                {{ $position->workers_count ?? 0 }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('company_positions.table.status') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                {{ $statusData['label'] }}
                            </p>
                        </div>

                    </div>
                </div>



            </div>
        </section>

        {{-- Details --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('company_positions.show.sections.basic') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">
                            {{ __('company_positions.form.name') }}
                        </span>

                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $position->name }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">
                            {{ __('company_positions.form.status') }}
                        </span>

                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('company_positions.show.sections.system') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">
                            {{ __('company_positions.table.created_at') }}
                        </span>

                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $position->created_at ? $position->created_at->format('Y-m-d h:i A') : '-' }}
                       f1b3d]">
                            {{ $position->created_at ? $position->created_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">
                            {{ __('company_positions.table.updated_at') }}
                        </span>

                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $position->updated_at ? $position->updated_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </section>

    </div>
@endsection
