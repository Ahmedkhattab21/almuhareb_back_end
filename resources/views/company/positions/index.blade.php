@extends('layouts.company')

@section('title', __('company_positions.page_title'))

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
        ];

        $positions = $positions ?? collect();
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_positions.breadcrumb_parent') }}
                        <span class="mx-1">›</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_positions.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('company_positions.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('company_positions.subtitle') }}
                    </p>
                </div>

                <div class="shrink-0">
                    <x-ui.button type="button" :full="false"
                        onclick="window.location.href='{{ Route::has('company.positions.create') ? route('company.positions.create') : '#' }}'"
                        class="min-w-[220px] rounded-2xl text-sm font-extrabold">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>

                        <span>{{ __('company_positions.add_new') }}</span>
                    </x-ui.button>
                </div>

            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        {{ __('company_positions.stats.total') }}
                    </p>

                    <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                        {{ number_format($stats['total'] ?? 0) }}
                    </h3>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        {{ __('company_positions.stats.active') }}
                    </p>

                    <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                        {{ number_format($stats['active'] ?? 0) }}
                    </h3>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">
                        {{ __('company_positions.stats.inactive') }}
                    </p>

                    <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                        {{ number_format($stats['inactive'] ?? 0) }}
                    </h3>
                </div>

            </div>
        </section>

        {{-- Filters + Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">

            <form method="GET" action="{{ route('company.positions.index') }}"
                class="border-b border-slate-100 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    <div class="relative w-full xl:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('company_positions.filters.search_placeholder') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white">

                        <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">

                        <select name="status"
                            class="h-12 min-w-[160px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('company_positions.filters.all_statuses') }}</option>

                            <option value="active" @selected(request('status') === 'active')>
                                {{ __('company_positions.status.active') }}
                            </option>

                            <option value="inactive" @selected(request('status') === 'inactive')>
                                {{ __('company_positions.status.inactive') }}
                            </option>
                        </select>

                        <select name="sort"
                            class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>
                                {{ __('company_positions.filters.id_asc') }}
                            </option>

                            <option value="latest" @selected(request('sort') === 'latest')>
                                {{ __('company_positions.filters.latest') }}
                            </option>

                            <option value="name_asc" @selected(request('sort') === 'name_asc')>
                                {{ __('company_positions.filters.name_asc') }}
                            </option>

                            <option value="name_desc" @selected(request('sort') === 'name_desc')>
                                {{ __('company_positions.filters.name_desc') }}
                            </option>
                        </select>

                        <button type="submit"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                            {{ __('company_positions.filters.apply') }}
                        </button>

                        <a href="{{ route('company.positions.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                            {{ __('company_positions.filters.reset') }}
                        </a>

                    </div>
                </div>
            </form>

            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">
                    {{ __('company_positions.table.title') }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ __('company_positions.table.showing') }}
                    {{ method_exists($positions, 'firstItem') ? $positions->firstItem() ?? 0 : 0 }}
                    -
                    {{ method_exists($positions, 'lastItem') ? $positions->lastItem() ?? 0 : count($positions) }}
                    {{ __('company_positions.table.from') }}
                    {{ method_exists($positions, 'total') ? $positions->total() : count($positions) }}
                    {{ __('company_positions.table.position') }}
                </p>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.id') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.name') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.status') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.created_at') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.updated_at') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_positions.table.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($positions as $position)
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

                                $showUrl = Route::has('company.positions.show') ? route('company.positions.show', $position->id) : '#';
                                $editUrl = Route::has('company.positions.edit') ? route('company.positions.edit', $position->id) : '#';
                                $destroyUrl = Route::has('company.positions.destroy') ? route('company.positions.destroy', $position->id) : '#';
                            @endphp

                            <tr onclick="window.location.href='{{ $showUrl }}'"
                                class="cursor-pointer transition hover:bg-slate-50">

                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    #{{ $position->id }}
                                </td>

                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    {{ $position->name }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $position->created_at ? $position->created_at->format('Y-m-d h:i A') : '-' }}
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $position->updated_at ? $position->updated_at->format('Y-m-d h:i A') : '-' }}
                                </td>

                                <td class="relative px-5 py-5" onclick="event.stopPropagation()">
                                    <details class="group relative inline-block">
                                        <summary
                                            class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="12" cy="19" r="2" />
                                            </svg>
                                        </summary>

                                        <div class="absolute end-0 z-50 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                            <a href="{{ $showUrl }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                                {{ __('company_positions.actions.show') }}
                                            </a>

                                            <a href="{{ $editUrl }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-[#0f1b3d]">
                                                {{ __('company_positions.actions.edit') }}
                                            </a>

                                            @if(Route::has('company.positions.destroy'))
                                                <form method="POST"
                                                    action="{{ $destroyUrl }}"
                                                    onsubmit="return confirm('{{ __('company_positions.actions.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50">
                                                        {{ __('company_positions.actions.delete') }}
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </details>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('company_positions.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="grid gap-4 p-4 xl:hidden">
                @forelse($positions as $position)
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

                        $showUrl = Route::has('company.positions.show') ? route('company.positions.show', $position->id) : '#';
                    @endphp

                    <div onclick="window.location.href='{{ $showUrl }}'"
                        class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">

                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-black text-[#0f1b3d]">
                                    #{{ $position->id }} - {{ $position->name }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ __('company_positions.table.created_at') }}:
                                    {{ $position->created_at ? $position->created_at->format('Y-m-d') : '-' }}
                                </p>
                            </div>

                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('company_positions.table.empty') }}
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($positions, 'links') && $positions->total() > 0)
                @php
                    $positions->appends(request()->query());
                @endphp

                <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_positions.pagination.showing') }}
                            <span class="text-[#0f1b3d]">{{ $positions->firstItem() ?? 0 }}</span>
                            {{ __('company_positions.pagination.to') }}
                            <span class="text-[#0f1b3d]">{{ $positions->lastItem() ?? 0 }}</span>
                            {{ __('company_positions.pagination.of') }}
                            <span class="text-[#0f1b3d]">{{ $positions->total() }}</span>
                            {{ __('company_positions.pagination.results') }}
                        </p>

                        @if ($positions->hasPages())
                            <div class="flex items-center justify-end gap-2">

                                @if ($positions->onFirstPage())
                                    <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('company_positions.pagination.previous') }}
                                    </span>
                                @else
                                    <a href="{{ $positions->previousPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('company_positions.pagination.previous') }}
                                    </a>
                                @endif

                                <span class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white">
                                    {{ __('company_positions.pagination.page') }}
                                    {{ $positions->currentPage() }}
                                    {{ __('company_positions.pagination.from') }}
                                    {{ $positions->lastPage() }}
                                </span>

                                @if ($positions->hasMorePages())
                                    <a href="{{ $positions->nextPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('company_positions.pagination.next') }}
                                    </a>
                                @else
                                    <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('company_positions.pagination.next') }}
                                    </span>
                                @endif

                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </section>

    </div>
@endsection
