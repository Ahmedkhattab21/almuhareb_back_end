@extends('layouts.lawyer')

@section('title', __('companies.show.page_title'))

@section('content')
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

        $companyInitials = mb_substr($company->company_name ?? '-', 0, 2);
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('lawyer_dashboard.sidebar.lawyer_panel') }}
                        <span class="mx-1">›</span>

                        <a href="{{ route('lawyer.companies.index') }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('lawyer_dashboard.sidebar.assigned_companies') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('companies.show.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4v18" />
                                <path d="M19 21V11l-6-4" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('companies.show.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('companies.show.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('lawyer.companies.index') }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>
                        {{ __('companies.show.back') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Company Card --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-[#eef3ff] text-2xl font-black text-[#0f1b3d]">
                            {{ $companyInitials }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-black text-[#0f1b3d]">
                                    {{ $company->company_name }}
                                </h2>

                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                    {{ $statusData['label'] }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-bold text-slate-500">
                                {{ $company->address ?? __('companies.table.no_address') }}
                            </p>

                            <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 xl:grid-cols-4">
                                <div>
                                    <p class="text-xs text-slate-400">{{ __('companies.form.email') }}</p>
                                    <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->email ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400">{{ __('companies.form.phone') }}</p>
                                    <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->phone ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400">{{ __('companies.form.tax_number') }}</p>
                                    <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->tax_number ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400">{{ __('companies.show.created_by') }}</p>
                                    <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->creator?->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-[#f8fbff] p-4">
                        <p class="text-xs font-bold text-slate-400">
                            {{ __('companies.show.company_id') }}
                        </p>

                        <p class="mt-1 text-2xl font-black text-[#0f1b3d]">
                            #{{ $company->id }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

      {{-- Stats --}}
<section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Workers --}}
    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="text-start">
                <p class="text-sm font-medium text-slate-500">
                    {{ __('companies.show.stats.workers') }}
                </p>

                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                    {{ number_format($stats['workers'] ?? 0) }}
                </h3>
            </div>

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Active Workers --}}
    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="text-start">
                <p class="text-sm font-medium text-slate-500">
                    {{ __('companies.show.stats.active_workers') }}
                </p>

                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                    {{ number_format($stats['active_workers'] ?? 0) }}
                </h3>
            </div>

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
            </div>
        </div>
    </div>

    {{-- All Tickets --}}
    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="text-start">
                <p class="text-sm font-medium text-slate-500">
                    {{ __('companies.show.stats.open_tickets') }}
                </p>

                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                    {{ number_format($stats['open_tickets'] ?? 0) }}
                </h3>
            </div>

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Assigned Lawyer --}}
    <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="text-start">
                <p class="text-sm font-medium text-slate-500">
                    {{ __('companies.show.stats.assigned_lawyer') }}
                </p>

                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                    {{ number_format($stats['assigned_lawyer'] ?? 0) }}
                </h3>
            </div>

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 3v18" />
                    <path d="M5 7h14" />
                    <path d="M6 7l-3 7h6l-3-7Z" />
                    <path d="M18 7l-3 7h6l-3-7Z" />
                </svg>
            </div>
        </div>
    </div>

</section>

        {{-- Details Cards --}}
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4v18" />
                        </svg>
                    </span>

                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('companies.show.sections.company_data') }}
                    </h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.form.email') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->email ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.form.phone') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.form.tax_number') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->tax_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.form.address') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-yellow-50 text-yellow-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M12 3v18" />
                            <path d="M5 7h14" />
                            <path d="M6 7l-3 7h6l-3-7Z" />
                            <path d="M18 7l-3 7h6l-3-7Z" />
                        </svg>
                    </span>

                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('companies.show.sections.legal_link') }}
                    </h3>
                </div>

                @if($company->lawyer)
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-400">{{ __('companies.show.lawyer_name') }}</p>
                            <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->lawyer->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">{{ __('companies.show.lawyer_email') }}</p>
                            <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->lawyer->email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">{{ __('companies.show.lawyer_phone') }}</p>
                            <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->lawyer->phone ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">{{ __('companies.show.lawyer_license') }}</p>
                            <p class="mt-1 font-bold text-[#0f1b3d]">{{ $company->lawyer->license_number ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl bg-[#f8fbff] p-5 text-center">
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('companies.show.no_lawyer') }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-50 text-green-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </span>

                    <h3 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('companies.show.sections.account') }}
                    </h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.form.status') }}</p>
                        <span class="mt-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.show.created_at') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">
                            {{ $company->created_at?->format('Y-m-d') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.show.updated_at') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">
                            {{ $company->updated_at?->format('Y-m-d') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">{{ __('companies.show.created_by') }}</p>
                        <p class="mt-1 font-bold text-[#0f1b3d]">
                            {{ $company->creator?->name ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Workers Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">
                    {{ __('companies.show.workers_title') }}
                </h2>
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.show.worker_table.id') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.show.worker_table.name') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.show.worker_table.phone') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.show.worker_table.nationality') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('companies.show.worker_table.status') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($workers as $worker)
                            @php
                                $workerStatus = $worker->status ?? 'active';

                                $workerStatusData = [
                                    'active' => [
                                        'label' => __('workers.status.active'),
                                        'class' => 'bg-green-50 text-green-700',
                                        'dot' => 'bg-green-500',
                                    ],
                                    'pending' => [
                                        'label' => __('workers.status.pending'),
                                        'class' => 'bg-yellow-50 text-yellow-700',
                                        'dot' => 'bg-yellow-500',
                                    ],
                                    'suspended' => [
                                        'label' => __('workers.status.suspended'),
                                        'class' => 'bg-red-50 text-red-700',
                                        'dot' => 'bg-red-500',
                                    ],
                                ][$workerStatus] ?? [
                                    'label' => __('workers.status.unknown'),
                                    'class' => 'bg-slate-100 text-slate-600',
                                    'dot' => 'bg-slate-400',
                                ];
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    #{{ $worker->id }}
                                </td>

                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    {{ $worker->name }}
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $worker->phone ?? '-' }}
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $worker->nationality?->nationality ?? '-' }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $workerStatusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $workerStatusData['dot'] }}"></span>
                                        {{ $workerStatusData['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('companies.show.no_workers') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid gap-4 p-4 lg:hidden">
                @forelse($workers as $worker)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="font-black text-[#0f1b3d]">
                            #{{ $worker->id }} - {{ $worker->name }}
                        </p>

                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('companies.show.worker_table.phone') }}</p>
                                <p class="mt-1 font-bold text-[#0f1b3d]">{{ $worker->phone ?? '-' }}</p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('companies.show.worker_table.nationality') }}</p>
                                <p class="mt-1 font-bold text-[#0f1b3d]">{{ $worker->nationality?->nationality ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('companies.show.no_workers') }}
                    </div>
                @endforelse
            </div>

            @if (method_exists($workers, 'links') && $workers->total() > 0)
                <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                    {{ $workers->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
