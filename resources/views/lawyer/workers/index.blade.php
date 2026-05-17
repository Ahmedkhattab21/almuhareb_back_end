@extends('layouts.lawyer')

@section('title', __('workers.page_title'))

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'active' => 0,
            'pending' => 0,
            'nationalities' => 0,
            'top_language' => '-',
        ];

        $workers = $workers ?? collect();
        $topLanguageLabel = $stats['top_language'] ?? '-';
    @endphp

    <div class="space-y-6 lg:space-y-8">
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('lawyer_dashboard.sidebar.lawyer_panel') }}
                        <span class="mx-1">›</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('lawyer_dashboard.sidebar.assigned_workers') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('lawyer_dashboard.sidebar.assigned_workers') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('workers.subtitle') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">{{ __('workers.stats.total') }}</p>
                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['total'] ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">{{ __('workers.stats.active') }}</p>
                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['active'] ?? 0) }}
                            </h3>
                            <p class="mt-2 text-xs font-bold text-green-600">{{ __('workers.stats.active_hint') }}</p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">{{ __('workers.stats.nationalities') }}</p>
                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['nationalities'] ?? 0) }}
                            </h3>
                            <p class="mt-2 text-xs font-bold text-slate-400">{{ __('workers.stats.nationalities_hint') }}</p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M2 12h20" />
                                <path d="M12 2a15.3 15.3 0 0 1 0 20" />
                                <path d="M12 2a15.3 15.3 0 0 0 0 20" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">{{ __('workers.stats.top_language') }}</p>
                            <h3 class="mt-5 text-3xl font-black leading-tight text-[#0f1b3d]">
                                {{ $topLanguageLabel ?: '-' }}
                            </h3>
                            <p class="mt-2 text-xs font-bold text-slate-400">{{ __('workers.stats.top_language_hint') }}</p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('lawyer.workers.index') }}" class="border-b border-slate-100 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div class="relative w-full xl:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('workers.filters.search_placeholder') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white">

                        <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <select name="status" class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('workers.filters.all_statuses') }}</option>
                            <option value="active" @selected(request('status') === 'active')>{{ __('workers.status.active') }}</option>
                            <option value="pending" @selected(request('status') === 'pending')>{{ __('workers.status.pending') }}</option>
                            <option value="suspended" @selected(request('status') === 'suspended')>{{ __('workers.status.suspended') }}</option>
                        </select>

                        <select name="company_id" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('workers.filters.all_companies') }}</option>
                            @foreach ($companies ?? [] as $company)
                                <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="nationality_id" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('workers.filters.all_nationalities') }}</option>
                            @foreach ($nationalities ?? [] as $nationality)
                                <option value="{{ $nationality->id }}" @selected((string) request('nationality_id') === (string) $nationality->id)>
                                    {{ $nationality->nationality }}
                                </option>
                            @endforeach
                        </select>

                        <select name="position_id" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('workers.filters.all_positions') }}</option>
                            @foreach ($positions ?? [] as $position)
                                <option value="{{ $position->id }}" @selected((string) request('position_id') === (string) $position->id)>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="prefered_language_id" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('workers.filters.all_languages') }}</option>
                            @foreach ($preferedLanguages ?? [] as $language)
                                <option value="{{ $language->id }}" @selected((string) request('prefered_language_id') === (string) $language->id)>
                                    {{ $language->prefered_language }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>{{ __('workers.filters.id_asc') }}</option>
                            <option value="latest" @selected(request('sort') === 'latest')>{{ __('workers.filters.latest') }}</option>
                            <option value="name_asc" @selected(request('sort') === 'name_asc')>{{ __('workers.filters.name_asc') }}</option>
                            <option value="name_desc" @selected(request('sort') === 'name_desc')>{{ __('workers.filters.name_desc') }}</option>
                        </select>

                        <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                            </svg>
                            {{ __('workers.filters.apply') }}
                        </button>

                        <a href="{{ route('lawyer.workers.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                            {{ __('workers.filters.reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">{{ __('workers.table.title') }}</h2>
                <p class="text-sm text-slate-500">
                    {{ __('workers.table.showing') }}
                    {{ method_exists($workers, 'firstItem') ? $workers->firstItem() ?? 0 : 0 }}
                    -
                    {{ method_exists($workers, 'lastItem') ? $workers->lastItem() ?? 0 : count($workers) }}
                    {{ __('workers.table.from') }}
                    {{ method_exists($workers, 'total') ? $workers->total() : count($workers) }}
                    {{ __('workers.table.worker') }}
                </p>
            </div>

            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full min-w-[1250px] text-sm">
                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.id') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.name') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.phone') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.company') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.nationality') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.prefered_language') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.iqama_number') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.position') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('workers.table.status') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($workers as $worker)
                            @php
                                $status = $worker->status ?? 'active';
                                $statusData = [
                                    'active' => ['label' => __('workers.status.active'), 'class' => 'bg-green-50 text-green-700', 'dot' => 'bg-green-500'],
                                    'pending' => ['label' => __('workers.status.pending'), 'class' => 'bg-yellow-50 text-yellow-700', 'dot' => 'bg-yellow-500'],
                                    'suspended' => ['label' => __('workers.status.suspended'), 'class' => 'bg-red-50 text-red-700', 'dot' => 'bg-red-500'],
                                ][$status] ?? ['label' => __('workers.status.unknown'), 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];

                                $workerName = $worker->name ?? '-';
                                $nationalityLabel = $worker->nationalityPreferredLanguage?->nationality?->nationality ?? '-';
                                $languageLabel = $worker->nationalityPreferredLanguage?->preferedLanguage?->prefered_language ?? '-';
                                $positionLabel = $worker->position?->name ?? '-';
                            @endphp

                            <tr onclick="window.location.href='{{ route('lawyer.workers.show', $worker->id) }}'"
                                class="cursor-pointer transition hover:bg-slate-50">
                                <td class="px-5 py-5 font-black text-[#0f1b3d]">#{{ $worker->id }}</td>
                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-3">
                                        @if (!empty($worker->image))
                                            <img src="{{ asset('storage/' . $worker->image) }}" alt="{{ $workerName }}" class="h-11 w-11 rounded-full object-cover">
                                        @else
                                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-sm font-black text-[#0f1b3d]">
                                                {{ mb_substr($workerName, 0, 1) }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="font-black text-[#0f1b3d]">{{ $workerName }}</p>
                                            <p class="text-xs text-slate-400">{{ $worker->email ?? __('workers.table.no_email') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5 text-slate-700">{{ $worker->phone ?? '-' }}</td>
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">{{ $worker->company?->company_name ?? __('workers.table.not_assigned') }}</td>
                                <td class="px-5 py-5 text-slate-700">{{ $nationalityLabel }}</td>
                                <td class="px-5 py-5 text-slate-700">{{ $languageLabel }}</td>
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">{{ $worker->iqama_number ?? '-' }}</td>
                                <td class="px-5 py-5 text-slate-700">{{ $positionLabel }}</td>
                                <td class="px-5 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('workers.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid gap-4 p-4 xl:hidden">
                @forelse($workers as $worker)
                    @php
                        $status = $worker->status ?? 'active';
                        $statusData = [
                            'active' => ['label' => __('workers.status.active'), 'class' => 'bg-green-50 text-green-700', 'dot' => 'bg-green-500'],
                            'pending' => ['label' => __('workers.status.pending'), 'class' => 'bg-yellow-50 text-yellow-700', 'dot' => 'bg-yellow-500'],
                            'suspended' => ['label' => __('workers.status.suspended'), 'class' => 'bg-red-50 text-red-700', 'dot' => 'bg-red-500'],
                        ][$status] ?? ['label' => __('workers.status.unknown'), 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];

                        $workerName = $worker->name ?? '-';
                        $nationalityLabel = $worker->nationalityPreferredLanguage?->nationality?->nationality ?? '-';
                        $languageLabel = $worker->nationalityPreferredLanguage?->preferedLanguage?->prefered_language ?? '-';
                        $positionLabel = $worker->position?->name ?? '-';
                    @endphp

                    <div onclick="window.location.href='{{ route('lawyer.workers.show', $worker->id) }}'"
                        class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if (!empty($worker->image))
                                    <img src="{{ asset('storage/' . $worker->image) }}" alt="{{ $workerName }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-sm font-black text-[#0f1b3d]">
                                        {{ mb_substr($workerName, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <p class="font-black text-[#0f1b3d]">#{{ $worker->id }} - {{ $workerName }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $worker->phone ?? '-' }}</p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.company') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $worker->company?->company_name ?? __('workers.table.not_assigned') }}</p>
                            </div>
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.nationality') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $nationalityLabel }}</p>
                            </div>
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.prefered_language') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $languageLabel }}</p>
                            </div>
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.iqama_number') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $worker->iqama_number ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.position') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $positionLabel }}</p>
                            </div>
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('workers.table.email') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $worker->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('workers.table.empty') }}
                    </div>
                @endforelse
            </div>

            @if (method_exists($workers, 'links') && $workers->total() > 0)
                @php
                    $workers->appends(request()->query());
                @endphp

                <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('workers.pagination.showing') }}
                            <span class="text-[#0f1b3d]">{{ $workers->firstItem() ?? 0 }}</span>
                            {{ __('workers.pagination.to') }}
                            <span class="text-[#0f1b3d]">{{ $workers->lastItem() ?? 0 }}</span>
                            {{ __('workers.pagination.of') }}
                            <span class="text-[#0f1b3d]">{{ $workers->total() }}</span>
                            {{ __('workers.pagination.results') }}
                        </p>

                        @if ($workers->hasPages())
                            <div class="flex items-center justify-end gap-2">
                                @if ($workers->onFirstPage())
                                    <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('workers.pagination.previous') }}
                                    </span>
                                @else
                                    <a href="{{ $workers->previousPageUrl() }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('workers.pagination.previous') }}
                                    </a>
                                @endif

                                <span class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white">
                                    {{ __('workers.pagination.page') }}
                                    {{ $workers->currentPage() }}
                                    {{ __('workers.pagination.from') }}
                                    {{ $workers->lastPage() }}
                                </span>

                                @if ($workers->hasMorePages())
                                    <a href="{{ $workers->nextPageUrl() }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('workers.pagination.next') }}
                                    </a>
                                @else
                                    <span class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('workers.pagination.next') }}
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
