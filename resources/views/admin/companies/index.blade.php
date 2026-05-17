@extends('layouts.app')

@section('title', __('companies.page_title'))

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'active' => 0,
            'open_disputes' => 0,
        ];

        $companies = $companies ?? collect();
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('companies.breadcrumb_parent') }}
                        <span class="mx-1">›</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('companies.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('companies.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('companies.subtitle') }}
                    </p>
                </div>

                {{-- Add Button --}}
                <div class="shrink-0">
                    <x-ui.button type="button" :full="false"
                        onclick="window.location.href='{{ route('admin.companies.create') }}'"
                        class="min-w-[220px] rounded-2xl text-sm font-extrabold">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4v18" />
                            <path d="M19 21V11l-6-4" />
                            <path d="M12 10v6" />
                            <path d="M9 13h6" />
                        </svg>

                        <span>{{ __('companies.add_new') }}</span>
                    </x-ui.button>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

                {{-- Total Companies --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">

                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('companies.stats.total') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['total'] ?? 0) }}
                            </h3>
                        </div>

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4v18" />
                                <path d="M19 21V11l-6-4" />
                            </svg>
                        </div>

                    </div>
                </div>

                {{-- Active Companies --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">

                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('companies.stats.active') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['active'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-green-600">
                                {{ __('companies.stats.active_hint') }}
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>

                    </div>
                </div>

                {{-- Open Disputes --}}
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">

                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('companies.stats.open_disputes') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['open_disputes'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-red-600">
                                {{ __('companies.stats.open_disputes_hint') }}
                            </p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 9v4" />
                                <path d="M12 17h.01" />
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            </svg>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        {{-- Filters + Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">

            {{-- Filters Header --}}
            <form method="GET" action="{{ route('admin.companies.index') }}"
                class="border-b border-slate-100 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    {{-- Search --}}
                    <div class="relative w-full xl:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('companies.filters.search_placeholder') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white">

                        <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>

                    {{-- Filters --}}
                    <div class="flex flex-wrap items-center gap-3">

                        <select name="status"
                            class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('companies.filters.all_statuses') }}</option>

                            <option value="active" @selected(request('status') === 'active')>
                                {{ __('companies.status.active') }}
                            </option>

                            <option value="pending" @selected(request('status') === 'pending')>
                                {{ __('companies.status.pending') }}
                            </option>

                            <option value="suspended" @selected(request('status') === 'suspended')>
                                {{ __('companies.status.suspended') }}
                            </option>
                        </select>

                            <select name="lawyer_id"
                                class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                                <option value="all">{{ __('companies.filters.all_lawyers') }}</option>

                                @foreach (($lawyers ?? [])->where('status', 'active') as $lawyer)
                                    <option value="{{ $lawyer->id }}" @selected((string) request('lawyer_id') === (string) $lawyer->id)>
                                        {{ $lawyer->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="sort"
                                class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                                <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>
                                    {{ __('companies.filters.id_asc') }}
                                </option>

                                <option value="latest" @selected(request('sort') === 'latest')>
                                    {{ __('companies.filters.latest') }}
                                </option>

                                <option value="oldest" @selected(request('sort') === 'oldest')>
                                    {{ __('companies.filters.oldest') }}
                                </option>

                                <option value="name_asc" @selected(request('sort') === 'name_asc')>
                                    {{ __('companies.filters.name_asc') }}
                                </option>

                                <option value="name_desc" @selected(request('sort') === 'name_desc')>
                                    {{ __('companies.filters.name_desc') }}
                                </option>
                            </select>

                            <button type="submit"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                                </svg>

                                {{ __('companies.filters.apply') }}
                            </button>

                            <a href="{{ route('admin.companies.index') }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                                {{ __('companies.filters.reset') }}
                            </a>

                    </div>
                </div>
            </form>

            {{-- Table Title --}}
            <div
                class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">
                    {{ __('companies.table.title') }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ __('companies.table.showing') }}
                    {{ method_exists($companies, 'firstItem') ? $companies->firstItem() ?? 0 : 0 }}
                    -
                    {{ method_exists($companies, 'lastItem') ? $companies->lastItem() ?? 0 : count($companies) }}
                    {{ __('companies.table.from') }}
                    {{ method_exists($companies, 'total') ? $companies->total() : count($companies) }}
                    {{ __('companies.table.company') }}
                </p>
            </div>

            {{-- Desktop / Tablet Table --}}
            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full min-w-[1350px] text-sm">

                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.id') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.company_name') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.phone') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.email') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.status') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.tax_number') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.lawyer') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.address') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.table.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($companies as $company)
                            @php
                                $status = $company->status ?? 'active';

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

                                $initials = mb_substr($company->company_name ?? '-', 0, 2);
                            @endphp

                            <tr onclick="window.location.href='{{ route('admin.companies.show', $company->id) }}'"
                                class="cursor-pointer transition hover:bg-slate-50">
                                {{-- ID --}}
                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    #{{ $company->id }}
                                </td>

                                {{-- Company Name --}}
                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                            {{ $initials }}
                                        </div>

                                        <div>
                                            <p class="font-black text-[#0f1b3d]">
                                                {{ $company->company_name }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ __('companies.table.company') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Phone --}}
                                <td class="px-5 py-5 text-slate-700">
                                    {{ $company->phone ?? '-' }}
                                </td>

                                {{-- Email --}}
                                <td class="px-5 py-5 text-slate-700">
                                    {{ $company->email ?? '-' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                {{-- Tax Number --}}
                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ $company->tax_number ?? '-' }}
                                    </span>
                                </td>

                                {{-- Lawyer --}}
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    {{ $company->lawyer?->name ?? __('companies.table.not_assigned') }}
                                </td>

                                {{-- Address --}}
                                <td class="px-5 py-5 text-slate-700">
                                    {{ $company->address ?? __('companies.table.no_address') }}
                                </td>

                                {{-- Actions --}}
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

                                        <div
                                            class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                            <a href="{{ route('admin.companies.show', $company->id) }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>

                                                {{ __('companies.actions.show') }}
                                            </a>

                                            <a href="{{ route('admin.companies.edit', $company->id) }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-[#0f1b3d]">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                </svg>

                                                {{ __('companies.actions.edit') }}
                                            </a>

                                            <form method="POST"
                                                action="{{ Route::has('admin.companies.destroy') ? route('admin.companies.destroy', $company->id) : '#' }}"
                                                onsubmit="return confirm('{{ __('companies.actions.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path d="M3 6h18" />
                                                        <path d="M8 6V4h8v2" />
                                                        <path d="M19 6l-1 14H6L5 6" />
                                                    </svg>

                                                    {{ __('companies.actions.delete') }}
                                                </button>
                                            </form>

                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('companies.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Mobile / Small Tablet Cards --}}
            <div class="grid gap-4 p-4 xl:hidden">
                @forelse($companies as $company)
                    @php
                        $status = $company->status ?? 'active';

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

                        $initials = mb_substr($company->company_name ?? '-', 0, 2);
                    @endphp

                    <div onclick="window.location.href='{{ route('admin.companies.show', $company->id) }}'"
                        class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                    {{ $initials }}
                                </div>

                                <div>
                                    <p class="font-black text-[#0f1b3d]">
                                        #{{ $company->id }} - {{ $company->company_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $company->email }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                    {{ $statusData['label'] }}
                                </span>

                                <details class="group relative inline-block">
                                    <summary
                                        class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="12" cy="19" r="2" />
                                        </svg>
                                    </summary>

                                    <div
                                        class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                        <a href="{{ route('admin.companies.show', $company->id) }}"
                                            class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                            {{ __('companies.actions.show') }}
                                        </a>

                                        <a href="{{ route('admin.companies.edit', $company->id) }}"
                                            class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                            {{ __('companies.actions.edit') }}
                                        </a>

                                        <form method="POST"
                                            action="{{ Route::has('admin.companies.destroy') ? route('admin.companies.destroy', $company->id) : '#' }}"
                                            onsubmit="return confirm('{{ __('companies.actions.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50">
                                                {{ __('companies.actions.delete') }}
                                            </button>
                                        </form>

                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.phone') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $company->phone ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.tax_number') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $company->tax_number ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.lawyer') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $company->lawyer?->name ?? __('companies.table.not_assigned') }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.address') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $company->address ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.status') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $statusData['label'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('companies.table.id') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    #{{ $company->id }}
                                </p>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('companies.table.empty') }}
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($companies, 'links') && $companies->total() > 0)
                @php
                    $companies->appends(request()->query());
                @endphp

                <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Pagination Info --}}
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('companies.pagination.showing') }}
                            <span class="text-[#0f1b3d]">{{ $companies->firstItem() ?? 0 }}</span>
                            {{ __('companies.pagination.to') }}
                            <span class="text-[#0f1b3d]">{{ $companies->lastItem() ?? 0 }}</span>
                            {{ __('companies.pagination.of') }}
                            <span class="text-[#0f1b3d]">{{ $companies->total() }}</span>
                            {{ __('companies.pagination.results') }}
                        </p>

                        {{-- Pagination Buttons --}}
                        @if ($companies->hasPages())
                            <div class="flex items-center justify-end gap-2">

                                {{-- Previous --}}
                                @if ($companies->onFirstPage())
                                    <span
                                        class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('companies.pagination.previous') }}
                                    </span>
                                @else
                                    <a href="{{ $companies->previousPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('companies.pagination.previous') }}
                                    </a>
                                @endif

                                {{-- Current Page --}}
                                <span
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white">
                                    {{ __('companies.pagination.page') }}
                                    {{ $companies->currentPage() }}
                                    {{ __('companies.pagination.from') }}
                                    {{ $companies->lastPage() }}
                                </span>

                                {{-- Next --}}
                                @if ($companies->hasMorePages())
                                    <a href="{{ $companies->nextPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('companies.pagination.next') }}
                                    </a>
                                @else
                                    <span
                                        class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('companies.pagination.next') }}
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
