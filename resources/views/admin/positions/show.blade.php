@extends('layouts.app')

@section('title', __('positions.show.page_title'))

@section('content')
    @php
        $status = $position->status ?? 'active';

        $statusData = [
            'active' => [
                'label' => __('positions.status.active'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'inactive' => [
                'label' => __('positions.status.inactive'),
                'class' => 'bg-red-50 text-red-700',
                'dot' => 'bg-red-500',
            ],
        ][$status] ?? [
            'label' => __('positions.status.unknown'),
            'class' => 'bg-slate-100 text-slate-600',
            'dot' => 'bg-slate-400',
        ];
    @endphp

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
                            {{ __('positions.show.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('positions.show.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('positions.show.subtitle') }}
                    </p>
                </div>

                <a href="{{ route('admin.positions.index') }}"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                    {{ __('positions.show.back') }}
                </a>

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
                                {{ __('positions.table.id') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                #{{ $position->id }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('positions.show.workers_count') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                {{ $position->workers_count ?? 0 }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#f8fbff] p-4">
                            <p class="text-xs font-bold text-slate-400">
                                {{ __('positions.table.status') }}
                            </p>

                            <p class="mt-2 text-lg font-black text-[#0f1b3d]">
                                {{ $statusData['label'] }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-[#f8fbff] p-5 text-center">
                    <p class="text-sm font-bold text-slate-500">
                        {{ __('positions.show.position_id') }}
                    </p>

                    <h3 class="mt-3 text-4xl font-black text-[#0f1b3d]">
                        #{{ $position->id }}
                    </h3>
                </div>

            </div>
        </section>

        {{-- Details --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('positions.show.sections.basic') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('positions.form.name') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $position->name }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('positions.form.status') }}</span>
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
                        {{ __('positions.show.sections.system') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('positions.table.created_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $position->created_at ? $position->created_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('positions.table.updated_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $position->updated_at ? $position->updated_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </section>

    </div>
@endsection
