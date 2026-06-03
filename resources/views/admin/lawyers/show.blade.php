@extends('layouts.app')

@section('title', __('lawyers.show.page_title'))

@section('content')
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
        $avatarUrl = !empty($lawyer->avatar) ? asset('storage/' . $lawyer->avatar) : null;
        $closedTicketsHistory = collect($closedTicketsHistory ?? []);
        $maxClosedTicketsHistory = max(1, (int) $closedTicketsHistory->max('count'));
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('lawyers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a
                            href="{{ route('admin.lawyers.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]"
                        >
                            {{ __('lawyers.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('lawyers.show.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('lawyers.show.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('lawyers.show.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-3">

                    @if(Route::has('admin.lawyers.edit'))
                        <x-ui.button
                            type="button"
                            :full="false"
                            onclick="window.location.href='{{ route('admin.lawyers.edit', $lawyer->id) }}'"
                            class="!w-auto !min-w-[180px] !flex-none rounded-2xl px-6 text-sm font-extrabold"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>

                            <span>{{ __('lawyers.show.edit') }}</span>
                        </x-ui.button>
                    @endif

                    @if(Route::has('admin.companies.index'))
                        <a
                            href="{{ route('admin.companies.index', ['lawyer_id' => $lawyer->id]) }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-[#5368aa] bg-white px-6 text-sm font-extrabold text-[#5368aa] transition hover:bg-[#eef3ff]"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4v18" />
                                <path d="M19 21V11l-6-4" />
                            </svg>

                            {{ __('lawyers.show.related_companies') }}
                        </a>
                    @endif

                    <a
                        href="{{ route('admin.lawyers.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('lawyers.show.back') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Profile Card --}}
        <section class="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-sm">
            <div class="p-5 lg:p-6">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        @if($avatarUrl)
                            <img
                                src="{{ $avatarUrl }}"
                                alt="{{ $lawyerName }}"
                                class="h-28 w-28 rounded-3xl border border-slate-200 object-cover shadow-sm"
                            >
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-3xl bg-[#edf3ff] text-4xl font-black text-[#0f1b3d]">
                                {{ mb_substr($lawyerName, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-black text-[#0f1b3d]">
                                    {{ $lawyerName }}
                                </h2>

                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                    {{ $statusData['label'] }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-bold text-slate-500">
                                {{ __('lawyers.show.lawyer_role') }}
                            </p>

                            <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 xl:grid-cols-3">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="h-4 w-4 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 4h16v16H4z" />
                                        <path d="m22 6-10 7L2 6" />
                                    </svg>

                                    <span class="font-bold">{{ $lawyer->email ?? '-' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="h-4 w-4 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.61a2 2 0 0 1-.45 2.11L9 10.59a16 16 0 0 0 4.41 4.41l1.15-1.15a2 2 0 0 1 2.11-.45c.84.27 1.71.47 2.61.59A2 2 0 0 1 22 16.92Z" />
                                    </svg>

                                    <span class="font-bold">{{ $lawyer->phone ?? '-' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="h-4 w-4 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v5l3 3" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>

                                    <span class="font-bold">
                                        {{ __('lawyers.show.created_at') }}:
                                        {{ $lawyer->created_at?->format('Y-m-d') ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('lawyers.show.stats.companies') }}</p>
                        <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['companies'] ?? 0) }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4v18" />
                            <path d="M19 21V11l-6-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('lawyers.show.stats.workers') }}</p>
                        <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['workers'] ?? 0) }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('lawyers.show.stats.total_tickets') }}</p>
                        <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['total_tickets'] ?? 0) }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('lawyers.show.stats.active_cases') }}</p>
                        <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['open_tickets'] ?? 0) }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">إنجاز اليوم</p>
                        <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['closed_today_tickets'] ?? 0) }}</h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                            <path d="M3 3v18h18" />
                        </svg>
                    </div>
                </div>
            </div>

        </section>

        {{-- Details --}}
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            {{-- Personal Info --}}
            <div class="rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('lawyers.show.personal_info') }}
                    </h3>

                    <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.table.name') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->name ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.table.email') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->email ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.table.phone') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->phone ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.table.status') }}</span>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Performance --}}
            <div class="rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('lawyers.show.performance') }}
                    </h3>

                    <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v18h18" />
                        <path d="m19 9-5 5-4-4-3 3" />
                    </svg>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.table.cases_count') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ number_format($stats['total_tickets'] ?? 0) }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">التذاكر المقفولة اليوم</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ number_format($stats['closed_today_tickets'] ?? 0) }}</span>
                    </div>

                    <div class="py-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-500">{{ __('lawyers.show.case_categories') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ number_format(($caseCategories ?? collect())->count()) }}</span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse($caseCategories ?? [] as $category)
                                <span class="rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                                    {{ $category->name }}
                                </span>
                            @empty
                                <span class="text-xs font-bold text-slate-400">{{ __('lawyers.show.no_case_categories') }}</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="py-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-500">سجل الإغلاق السابق</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ number_format($closedTicketsHistory->sum('count')) }}</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse($closedTicketsHistory as $day)
                                @php
                                    $dayCount = (int) data_get($day, 'count', 0);
                                    $width = max(8, round(($dayCount / $maxClosedTicketsHistory) * 100));
                                @endphp

                                <div>
                                    <div class="mb-1 flex items-center justify-between text-xs font-bold text-slate-500">
                                        <span>{{ data_get($day, 'label') }} - {{ data_get($day, 'short_date') }}</span>
                                        <span>{{ number_format($dayCount) }}</span>
                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $width }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <span class="text-xs font-bold text-slate-400">لا توجد بيانات إغلاق سابقة.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Info --}}
            <div class="rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('lawyers.show.system_info') }}
                    </h3>

                    <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.92 4a1.65 1.65 0 0 0 1-1.51V2a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15 3.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.14.31.22.65.22 1H21a2 2 0 1 1 0 4h-.09A1.65 1.65 0 0 0 19.4 15Z" />
                    </svg>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.show.created_by') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->creator?->name ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.show.admin') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->admin?->name ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.show.created_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->created_at ? $lawyer->created_at->format('Y-m-d H:i') : '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-sm font-bold text-slate-500">{{ __('lawyers.show.updated_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->updated_at ? $lawyer->updated_at->format('Y-m-d H:i') : '-' }}</span>
                    </div>
                </div>
            </div>

        </section>

        {{-- Related Companies + Tickets --}}
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">

            {{-- Related Companies --}}
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('lawyers.show.companies_title') }}
                    </h3>

                    @if(Route::has('admin.companies.index'))
                        <a
                            href="{{ route('admin.companies.index', ['lawyer_id' => $lawyer->id]) }}"
                            class="text-sm font-bold text-blue-700 transition hover:text-blue-900"
                        >
                            {{ __('lawyers.show.view_all') }}
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[650px] text-sm">
                        <thead class="bg-[#f8fbff] text-slate-500">
                            <tr>
                                <th class="px-5 py-4 text-start font-bold">{{ __('companies.table.id') }}</th>
                                <th class="px-5 py-4 text-start font-bold">{{ __('companies.table.company_name') }}</th>
                                <th class="px-5 py-4 text-start font-bold">{{ __('companies.table.email') }}</th>
                                <th class="px-5 py-4 text-start font-bold">{{ __('lawyers.show.case_categories') }}</th>
                                <th class="px-5 py-4 text-start font-bold">{{ __('companies.table.status') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($companies as $company)
                                @php
                                    $companyStatus = $company->status ?? 'active';

                                    $companyStatusLabel = \Illuminate\Support\Facades\Lang::has('companies.status.' . $companyStatus)
                                        ? __('companies.status.' . $companyStatus)
                                        : $companyStatus;
                                @endphp

                                <tr
                                    @if(Route::has('admin.companies.show'))
                                        onclick="window.location.href='{{ route('admin.companies.show', $company->id) }}'"
                                        class="cursor-pointer transition hover:bg-slate-50"
                                    @else
                                        class="transition hover:bg-slate-50"
                                    @endif
                                >
                                    <td class="px-5 py-4 font-black text-[#0f1b3d]">
                                        #{{ $company->id }}
                                    </td>

                                    <td class="px-5 py-4 font-bold text-[#0f1b3d]">
                                        {{ $company->company_name ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-600">
                                        {{ $company->email ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex max-w-[260px] flex-wrap gap-2">
                                            @forelse($company->case_categories ?? [] as $category)
                                                <span class="rounded-full bg-[#eef3ff] px-3 py-1 text-[11px] font-extrabold text-[#5368aa]">
                                                    {{ $category->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs font-bold text-slate-400">{{ __('lawyers.show.no_case_categories') }}</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            {{ $companyStatusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                        {{ __('lawyers.show.no_companies') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($companies, 'links') && $companies->hasPages())
                    <div class="border-t border-slate-100 bg-[#f8fbff] px-5 py-4">
                        {{ $companies->links() }}
                    </div>
                @endif
            </div>

            {{-- Latest Tickets --}}
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('lawyers.show.latest_tickets') }}
                    </h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($latestTickets as $ticket)
                        @php
                            $ticketTitle = $ticket->title
                                ?? $ticket->subject
                                ?? $ticket->message_original
                                ?? $ticket->description_original
                                ?? __('lawyers.show.ticket');

                            $ticketStatus = $ticket->status ?? '-';
                        @endphp

                        <div
                            @if(Route::has('admin.tickets.show') && isset($ticket->id))
                                onclick="window.location.href='{{ route('admin.tickets.show', $ticket->id) }}'"
                                class="cursor-pointer p-5 transition hover:bg-slate-50"
                            @else
                                class="p-5"
                            @endif
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-black text-[#0f1b3d]">
                                        #{{ $ticket->id ?? '-' }} - {{ \Illuminate\Support\Str::limit($ticketTitle, 80) }}
                                    </p>

                                    <p class="mt-2 text-xs font-bold text-slate-400">
                                        {{ isset($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('Y-m-d H:i') : '-' }}
                                    </p>
                                </div>

                                <span class="rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-bold text-[#5368aa]">
                                    {{ $ticketStatus }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            {{ __('lawyers.show.no_tickets') }}
                        </div>
                    @endforelse
                </div>
            </div>

        </section>

    </div>
@endsection
