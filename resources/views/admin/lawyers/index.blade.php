@extends('layouts.app')

@section('title', __('lawyers.page_title'))

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'active' => 0,
        ];

        $lawyers = $lawyers ?? collect();
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Top Header + Stats --}}
        <section class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('lawyers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('lawyers.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('lawyers.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('lawyers.subtitle') }}
                    </p>
                </div>

                <div class="shrink-0">
                    <x-ui.button
                        type="button"
                        :full="false"
                        onclick="window.location.href='{{ Route::has('admin.lawyers.create') ? route('admin.lawyers.create') : '#' }}'"
                        class="min-w-[220px] rounded-2xl text-sm font-extrabold"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M19 8v6" />
                            <path d="M16 11h6" />
                        </svg>

                        <span>{{ __('lawyers.add_new') }}</span>
                    </x-ui.button>
                </div>

            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- Total --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyers.stats.total') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['total'] ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyers.stats.active') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['active'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-slate-400">
                                {{ __('lawyers.available') }}
                            </p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
        </section>

   {{-- Filters + Table --}}
<section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.lawyers.index') }}" class="border-b border-slate-100 bg-white p-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

            <div class="relative w-full xl:max-w-md">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('lawyers.filters.search_placeholder') }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                >

                <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" />
                </svg>
            </div>

            <div class="flex flex-wrap items-center gap-3">

                <select
                    name="status"
                    class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white"
                >
                    <option value="all">{{ __('lawyers.filters.all_statuses') }}</option>

                    <option value="active" @selected(request('status') === 'active')>
                        {{ __('lawyers.status.active') }}
                    </option>

                    <option value="pending" @selected(request('status') === 'pending')>
                        {{ __('lawyers.status.pending') }}
                    </option>

                    <option value="suspended" @selected(request('status') === 'suspended')>
                        {{ __('lawyers.status.suspended') }}
                    </option>
                </select>

                <select
                    name="sort"
                    class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white"
                >
                    <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>
                        {{ __('lawyers.filters.id_asc') }}
                    </option>

                    <option value="latest" @selected(request('sort') === 'latest')>
                        {{ __('lawyers.filters.latest') }}
                    </option>

                    <option value="cases_desc" @selected(request('sort') === 'cases_desc')>
                        {{ __('lawyers.filters.most_cases') }}
                    </option>

                    <option value="name_asc" @selected(request('sort') === 'name_asc')>
                        {{ __('lawyers.filters.name_asc') }}
                    </option>

                    <option value="name_desc" @selected(request('sort') === 'name_desc')>
                        {{ __('lawyers.filters.name_desc') }}
                    </option>
                </select>

                <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                    </svg>

                    {{ __('lawyers.filters.apply') }}
                </button>

                <a href="{{ route('admin.lawyers.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                    {{ __('lawyers.filters.reset') }}
                </a>

            </div>
        </div>
    </form>

    {{-- Table Title --}}
    <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-black text-[#0f1b3d]">
            {{ __('lawyers.table.title') }}
        </h2>

        <p class="text-sm text-slate-500">
            {{ __('lawyers.table.showing') }}
            {{ method_exists($lawyers, 'firstItem') ? ($lawyers->firstItem() ?? 0) : 0 }}
            -
            {{ method_exists($lawyers, 'lastItem') ? ($lawyers->lastItem() ?? 0) : count($lawyers) }}
            {{ __('lawyers.table.from') }}
            {{ method_exists($lawyers, 'total') ? $lawyers->total() : count($lawyers) }}
            {{ __('lawyers.table.lawyer') }}
        </p>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden overflow-x-auto xl:block">
        <table class="w-full min-w-[1100px] text-sm">
            <thead class="bg-[#f8fbff] text-slate-500">
                <tr>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.id') }}</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.name') }}</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.phone') }}</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.email') }}</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.status') }}</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.cases_count') }}</th>
                    <th class="px-5 py-5 text-start font-bold">إنجاز اليوم</th>
                    <th class="px-5 py-5 text-start font-bold">{{ __('lawyers.table.actions') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($lawyers as $lawyer)
                    @php
                        $status = $lawyer->status ?? 'active';

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

                        $ticketsCount = $lawyer->tickets_count ?? 0;
                        $closedTodayTicketsCount = $lawyer->closed_today_tickets_count ?? 0;

                        $showUrl = Route::has('admin.lawyers.show')
                            ? route('admin.lawyers.show', $lawyer->id)
                            : route('admin.lawyers.edit', $lawyer->id);
                    @endphp

                    <tr
                        onclick="window.location.href='{{ $showUrl }}'"
                        class="cursor-pointer transition hover:bg-slate-50"
                    >

                        <td class="px-5 py-5 font-black text-[#0f1b3d]">
                            #{{ $lawyer->id }}
                        </td>

                        <td class="px-5 py-5">
                            <div class="flex items-center gap-3">
                                @if (!empty($lawyer->avatar))
                                    <img src="{{ asset('storage/' . $lawyer->avatar) }}" alt="{{ $lawyerName }}" class="h-11 w-11 rounded-full object-cover">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-sm font-black text-[#0f1b3d]">
                                        {{ mb_substr($lawyerName, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <p class="font-black text-[#0f1b3d]">
                                        {{ $lawyerName }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        {{ __('lawyers.table.lawyer_role') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-5 text-slate-700">
                            {{ $lawyer->phone ?? '-' }}
                        </td>

                        <td class="px-5 py-5 text-slate-700">
                            {{ $lawyer->email ?? '-' }}
                        </td>

                        <td class="px-5 py-5">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </td>

                        <td class="px-5 py-5 font-black text-[#0f1b3d]">
                            {{ number_format($ticketsCount) }}
                        </td>

                        <td class="px-5 py-5">
                            <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-xs font-extrabold text-green-700">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                {{ number_format($closedTodayTicketsCount) }} تذكرة
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="relative px-5 py-5" onclick="event.stopPropagation()">
                            <details class="group relative inline-block">
                                <summary
                                    class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden"
                                >
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="12" cy="19" r="2" />
                                    </svg>
                                </summary>

                                <div class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                    @if(Route::has('admin.lawyers.show'))
                                        <a
                                            href="{{ route('admin.lawyers.show', $lawyer->id) }}"
                                            class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>

                                            {{ __('lawyers.actions.show') }}
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('admin.lawyers.edit', $lawyer->id) }}"
                                        class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-[#0f1b3d]"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>

                                        {{ __('lawyers.actions.edit') }}
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ Route::has('admin.lawyers.destroy') ? route('admin.lawyers.destroy', $lawyer->id) : '#' }}"
                                        onsubmit="return confirm('{{ __('lawyers.actions.confirm_delete') }}')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <path d="M19 6l-1 14H6L5 6" />
                                            </svg>

                                            {{ __('lawyers.actions.delete') }}
                                        </button>
                                    </form>

                                </div>
                            </details>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                            {{ __('lawyers.table.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="grid gap-4 p-4 xl:hidden">
        @forelse($lawyers as $lawyer)
            @php
                $status = $lawyer->status ?? 'active';

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
                $ticketsCount = $lawyer->tickets_count ?? 0;
                $closedTodayTicketsCount = $lawyer->closed_today_tickets_count ?? 0;

                $showUrl = Route::has('admin.lawyers.show')
                    ? route('admin.lawyers.show', $lawyer->id)
                    : route('admin.lawyers.edit', $lawyer->id);
            @endphp

            <div
                onclick="window.location.href='{{ $showUrl }}'"
                class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50"
            >

                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if (!empty($lawyer->avatar))
                            <img src="{{ asset('storage/' . $lawyer->avatar) }}" alt="{{ $lawyerName }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-sm font-black text-[#0f1b3d]">
                                {{ mb_substr($lawyerName, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <p class="font-black text-[#0f1b3d]">
                                #{{ $lawyer->id }} - {{ $lawyerName }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $lawyer->email ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>

                        <details class="group relative inline-block">
                            <summary
                                class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="5" r="2" />
                                    <circle cx="12" cy="12" r="2" />
                                    <circle cx="12" cy="19" r="2" />
                                </svg>
                            </summary>

                            <div class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                @if(Route::has('admin.lawyers.show'))
                                    <a
                                        href="{{ route('admin.lawyers.show', $lawyer->id) }}"
                                        class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        {{ __('lawyers.actions.show') }}
                                    </a>
                                @endif

                                <a
                                    href="{{ route('admin.lawyers.edit', $lawyer->id) }}"
                                    class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                >
                                    {{ __('lawyers.actions.edit') }}
                                </a>

                                <form
                                    method="POST"
                                    action="{{ Route::has('admin.lawyers.destroy') ? route('admin.lawyers.destroy', $lawyer->id) : '#' }}"
                                    onsubmit="return confirm('{{ __('lawyers.actions.confirm_delete') }}')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50"
                                    >
                                        {{ __('lawyers.actions.delete') }}
                                    </button>
                                </form>

                            </div>
                        </details>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">

                    <div class="rounded-xl bg-[#f8fbff] p-3">
                        <p class="text-xs text-slate-400">
                            {{ __('lawyers.table.phone') }}
                        </p>

                        <p class="mt-1 font-black text-[#0f1b3d]">
                            {{ $lawyer->phone ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-[#f8fbff] p-3">
                        <p class="text-xs text-slate-400">
                            {{ __('lawyers.table.cases') }}
                        </p>

                        <p class="mt-1 font-black text-[#0f1b3d]">
                            {{ number_format($ticketsCount) }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-green-50 p-3">
                        <p class="text-xs text-green-700">
                            إنجاز اليوم
                        </p>

                        <p class="mt-1 font-black text-green-800">
                            {{ number_format($closedTodayTicketsCount) }} تذكرة
                        </p>
                    </div>

                </div>

            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                {{ __('lawyers.table.empty') }}
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if (method_exists($lawyers, 'links') && $lawyers->total() > 0)
        @php
            $lawyers->appends(request()->query());
        @endphp

        <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm font-bold text-slate-500">
                    {{ __('lawyers.pagination.showing') }}
                    <span class="text-[#0f1b3d]">{{ $lawyers->firstItem() ?? 0 }}</span>
                    {{ __('lawyers.pagination.to') }}
                    <span class="text-[#0f1b3d]">{{ $lawyers->lastItem() ?? 0 }}</span>
                    {{ __('lawyers.pagination.of') }}
                    <span class="text-[#0f1b3d]">{{ $lawyers->total() }}</span>
                    {{ __('lawyers.pagination.results') }}
                </p>

                @if ($lawyers->hasPages())
                    <div class="flex items-center justify-end gap-2">

                        @if ($lawyers->onFirstPage())
                            <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                {{ __('lawyers.pagination.previous') }}
                            </span>
                        @else
                            <a href="{{ $lawyers->previousPageUrl() }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                {{ __('lawyers.pagination.previous') }}
                            </a>
                        @endif

                        <span class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white">
                            {{ __('lawyers.pagination.page') }}
                            {{ $lawyers->currentPage() }}
                            {{ __('lawyers.pagination.from') }}
                            {{ $lawyers->lastPage() }}
                        </span>

                        @if ($lawyers->hasMorePages())
                            <a href="{{ $lawyers->nextPageUrl() }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                {{ __('lawyers.pagination.next') }}
                            </a>
                        @else
                            <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                {{ __('lawyers.pagination.next') }}
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
