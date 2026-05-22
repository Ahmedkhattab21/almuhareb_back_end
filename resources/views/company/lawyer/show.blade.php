@extends('layouts.company')

@section('title', __('company_lawyer.show.page_title'))

@section('content')
    @php
        $companyName = $company->company_name ?? $company->name ?? '-';

        $dashboardUrl = Route::has('company.dashboard')
            ? route('company.dashboard')
            : url('/company/dashboard');

        $status = $lawyer->status ?? 'active';

        $statusData = [
            'active' => [
                'label' => __('company_lawyer.status.active'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'pending' => [
                'label' => __('company_lawyer.status.pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
                'dot' => 'bg-yellow-500',
            ],
            'suspended' => [
                'label' => __('company_lawyer.status.suspended'),
                'class' => 'bg-red-50 text-red-700',
                'dot' => 'bg-red-500',
            ],
        ][$status] ?? [
            'label' => __('company_lawyer.status.unknown'),
            'class' => 'bg-slate-100 text-slate-600',
            'dot' => 'bg-slate-400',
        ];

        $lawyerName = $lawyer->name ?? '-';

        $avatar = $lawyer->avatar ?? null;

        $avatarUrl = null;

        if ($avatar) {
            $avatarUrl = \Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://', '/'])
                ? $avatar
                : asset('storage/' . $avatar);
        }
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        <a href="{{ $dashboardUrl }}" class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_lawyer.breadcrumb_parent') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_lawyer.breadcrumb_current') }}
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
                                {{ __('company_lawyer.show.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('company_lawyer.show.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ $dashboardUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('company_lawyer.show.back') }}
                    </a>
                </div>

            </div>
        </section>

        @if(!$lawyer)
            {{-- Empty State --}}
            <section class="rounded-[30px] border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#eef3ff] text-[#5368aa]">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>

                <h2 class="mt-5 text-2xl font-black text-[#0f1b3d]">
                    {{ __('company_lawyer.empty.title') }}
                </h2>

                <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-500">
                    {{ __('company_lawyer.empty.subtitle') }}
                </p>
            </section>
        @else

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
                                    {{ __('company_lawyer.show.lawyer_role') }}
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
                                            <path d="M3 21h18" />
                                            <path d="M5 21V7l8-4v18" />
                                            <path d="M19 21V11l-6-4" />
                                        </svg>

                                        <span class="font-bold">{{ $companyName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {{-- Stats --}}
            <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.workers') }}</p>
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
                            <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.total_tickets') }}</p>
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
                            <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.active_cases') }}</p>
                            <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['active_cases_count'] ?? 0) }}</h3>
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
                            <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.response') }}</p>
                            <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">
                                {{ $stats['avg_response_hours'] ?? 0 }}h
                            </h3>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v5l3 3" />
                                <circle cx="12" cy="12" r="9" />
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
                            {{ __('company_lawyer.show.personal_info') }}
                        </h3>

                        <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>

                    <div class="divide-y divide-slate-100 p-5">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.name') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->name ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.email') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->email ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.phone') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $lawyer->phone ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.status') }}</span>
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
                            {{ __('company_lawyer.show.performance') }}
                        </h3>

                        <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18" />
                            <path d="m19 9-5 5-4-4-3 3" />
                        </svg>
                    </div>

                    <div class="divide-y divide-slate-100 p-5">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.cases_count') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ number_format($lawyer->active_cases_count ?? 0) }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.table.response_time') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ round(($lawyer->avg_response_minutes ?? 0) / 60, 1) }}h</span>
                        </div>
                    </div>
                </div>

                {{-- Company Info --}}
                <div class="rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <h3 class="text-lg font-black text-[#0f1b3d]">
                            {{ __('company_lawyer.show.company_info') }}
                        </h3>

                        <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4v18" />
                            <path d="M19 21V11l-6-4" />
                        </svg>
                    </div>

                    <div class="divide-y divide-slate-100 p-5">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.company.name') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $companyName }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.company.email') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $company->email ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.company.phone') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">{{ $company->phone ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-sm font-bold text-slate-500">{{ __('company_lawyer.company.assigned_at') }}</span>
                            <span class="text-sm font-black text-[#0f1b3d]">
                                {{ $company->updated_at ? $company->updated_at->format('Y-m-d H:i') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

            </section>

        @endif

    </div>
@endsection
