@extends('layouts.lawyer')

@section('title', __('lawyer_dashboard.page_title'))

@section('content')
@php
    $isAr = \Illuminate\Support\Str::startsWith(app()->getLocale(), 'ar');

    $t = function (string $key, string $ar, ?string $en = null) use ($isAr) {
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return $isAr ? $ar : ($en ?? $ar);
    };

    $stats = $stats ?? [
        'total_workers' => 0,
        'total_companies' => 0,
        'total_tickets' => 0,
        'open_tickets' => 0,
        'closed_tickets' => 0,
        'closed_today_tickets' => 0,
    ];

    $totalWorkers = (int) data_get($stats, 'total_workers', 0);
    $totalCompanies = (int) data_get($stats, 'total_companies', 0);
    $totalTickets = (int) data_get($stats, 'total_tickets', 0);
    $openTickets = (int) data_get($stats, 'open_tickets', 0);
    $closedTickets = (int) data_get($stats, 'closed_tickets', 0);
    $closedTodayTickets = (int) data_get($stats, 'closed_today_tickets', 0);

    $recentTickets = collect($recentTickets ?? []);
    $recentWorkers = collect($recentWorkers ?? []);
    $recentCompanies = collect($recentCompanies ?? []);

    $ticketsOverWeek = collect($ticketsOverWeek ?? []);
    $maxWeeklyTickets = max(1, (int) ($maxWeeklyTickets ?? $ticketsOverWeek->max('count') ?? 1));
    $closedTicketsHistory = collect($closedTicketsHistory ?? []);
    $maxClosedTicketsHistory = max(1, (int) ($maxClosedTicketsHistory ?? $closedTicketsHistory->max('count') ?? 1));

    $openPercent = $totalTickets > 0 ? round(($openTickets / $totalTickets) * 100) : 0;
    $closedPercent = $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100) : 0;

    $ticketStatusMeta = function (?string $status) use ($t) {
        $status = $status ?: 'open';

        return match ($status) {
            'closed', 'resolved' => [
                'label' => $t('lawyer_dashboard.ticket_status.closed', 'مغلقة', 'Closed'),
                'class' => 'bg-slate-100 text-slate-700',
            ],
            'pending', 'waiting_reply' => [
                'label' => $t('lawyer_dashboard.ticket_status.pending', 'قيد الانتظار', 'Pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
            ],
            'in_progress', 'review', 'in_review' => [
                'label' => $t('lawyer_dashboard.ticket_status.in_review', 'قيد المراجعة', 'In review'),
                'class' => 'bg-blue-50 text-blue-700',
            ],
            default => [
                'label' => $t('lawyer_dashboard.ticket_status.open', 'مفتوحة', 'Open'),
                'class' => 'bg-green-50 text-green-700',
            ],
        };
    };

    $workerStatusMeta = function (?string $status) use ($t) {
        $status = $status ?: 'active';

        return match ($status) {
            'pending' => [
                'label' => $t('lawyer_dashboard.worker_status.pending', 'قيد المراجعة', 'Pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
            ],
            'inactive', 'suspended' => [
                'label' => $t('lawyer_dashboard.worker_status.suspended', 'موقوف', 'Suspended'),
                'class' => 'bg-red-50 text-red-700',
            ],
            default => [
                'label' => $t('lawyer_dashboard.worker_status.active', 'نشط', 'Active'),
                'class' => 'bg-green-50 text-green-700',
            ],
        };
    };

    $companyStatusMeta = function (?string $status) use ($t) {
        $status = $status ?: 'active';

        return match ($status) {
            'pending' => [
                'label' => $t('lawyer_dashboard.company_status.pending', 'قيد المراجعة', 'Pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
            ],
            'inactive', 'suspended' => [
                'label' => $t('lawyer_dashboard.company_status.suspended', 'موقوفة', 'Suspended'),
                'class' => 'bg-red-50 text-red-700',
            ],
            default => [
                'label' => $t('lawyer_dashboard.company_status.active', 'نشطة', 'Active'),
                'class' => 'bg-green-50 text-green-700',
            ],
        };
    };

    $ticketTitle = function ($ticket) use ($t) {
        if (app()->getLocale() === 'ar') {
            return data_get($ticket, 'title_translated')
                ?? data_get($ticket, 'title')
                ?? data_get($ticket, 'title_original')
                ?? $t('lawyer_dashboard.common.no_title', 'بدون عنوان', 'No title');
        }

        return data_get($ticket, 'title_original')
            ?? data_get($ticket, 'title')
            ?? data_get($ticket, 'title_translated')
            ?? $t('lawyer_dashboard.common.no_title', 'بدون عنوان', 'No title');
    };
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#0f1b3d] sm:text-3xl">
                {{ $t('lawyer_dashboard.overview_title', 'نظرة عامة على لوحة المحامي', 'Lawyer Dashboard Overview') }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $t('lawyer_dashboard.overview_subtitle', 'تابع الشركات والعمال والتذاكر المرتبطة بك داخل النظام.', 'Track your assigned companies, workers, and tickets.') }}
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index') : '#' }}"
                class="rounded-xl bg-[#0f1b3d] px-5 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-slate-300"
            >
                {{ $t('lawyer_dashboard.actions.review_tickets', 'مراجعة التذاكر', 'Review Tickets') }}
            </a>

            <a
                href="{{ Route::has('lawyer.companies.index') ? route('lawyer.companies.index') : '#' }}"
                class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700"
            >
                {{ $t('lawyer_dashboard.actions.view_companies', 'عرض الشركات', 'View Companies') }}
            </a>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    {{ $t('lawyer_dashboard.stats.total_workers', 'إجمالي العمال', 'Total Workers') }}
                </p>

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-xl">
                    👷
                </div>
            </div>

            <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                {{ number_format($totalWorkers) }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    {{ $t('lawyer_dashboard.stats.total_companies', 'إجمالي الشركات', 'Total Companies') }}
                </p>

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-xl">
                    🏢
                </div>
            </div>

            <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                {{ number_format($totalCompanies) }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    {{ $t('lawyer_dashboard.stats.total_tickets', 'إجمالي التذاكر', 'Total Tickets') }}
                </p>

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-xl">
                    🎫
                </div>
            </div>

            <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                {{ number_format($totalTickets) }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    {{ $t('lawyer_dashboard.stats.closed_today', 'إغلاق اليوم', 'Closed Today') }}
                </p>

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                {{ number_format($closedTodayTickets) }}
            </p>
        </div>
    </section>

    {{-- Charts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Tickets Over Week --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ $t('lawyer_dashboard.tickets_over_time', 'التذاكر خلال الأسبوع', 'Tickets During the Week') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $t('lawyer_dashboard.last_7_days', 'آخر 7 أيام', 'Last 7 days') }}
                    </p>
                </div>
            </div>

            <div class="mt-8 h-72">
                <div class="flex h-60 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">
                    @forelse($ticketsOverWeek as $bar)
                        @php
                            $barCount = (int) data_get($bar, 'count', 0);
                            $barLabel = data_get($bar, 'label', '-');
                            $barShortDate = data_get($bar, 'short_date', '-');

                            $height = $maxWeeklyTickets > 0
                                ? max(6, round(($barCount / $maxWeeklyTickets) * 100))
                                : 6;
                        @endphp

                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-3">
                            <span class="text-xs font-bold text-[#0f1b3d]">
                                {{ $barCount }}
                            </span>

                            <div
                                class="w-full max-w-10 rounded-t-xl bg-[#5368aa]"
                                style="height: {{ $height }}%"
                            ></div>

                            <div class="text-center">
                                <span class="block text-[10px] text-slate-500 sm:text-xs">
                                    {{ $barLabel }}
                                </span>

                                <span class="block text-[10px] text-slate-400">
                                    {{ $barShortDate }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full w-full items-center justify-center text-sm text-slate-500">
                            {{ $t('lawyer_dashboard.common.no_data', 'لا توجد بيانات', 'No data available') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Ticket Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-[#0f1b3d]">
                {{ $t('lawyer_dashboard.ticket_status_chart', 'حالة التذاكر', 'Ticket Status') }}
            </h2>

            <div class="mt-8 flex flex-col items-center gap-6">
                <div
                    class="flex h-44 w-44 items-center justify-center rounded-full"
                    style="background: conic-gradient(#0f1b3d 0 {{ $openPercent }}%, #22c55e {{ $openPercent }}% {{ $openPercent + $closedPercent }}%, #e8eef8 {{ $openPercent + $closedPercent }}% 100%)"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#0f1b3d]">
                            {{ number_format($totalTickets) }}
                        </span>

                        <span class="text-xs text-slate-500">
                            {{ $t('lawyer_dashboard.ticket_status.all', 'كل التذاكر', 'All Tickets') }}
                        </span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-slate-200"></span>
                            {{ $t('lawyer_dashboard.ticket_status.all', 'كل التذاكر', 'All Tickets') }}
                        </span>

                        <span>
                            100% - {{ number_format($totalTickets) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                            {{ $t('lawyer_dashboard.ticket_status.open', 'التذاكر المفتوحة', 'Open Tickets') }}
                        </span>

                        <span>
                            {{ $openPercent }}% - {{ number_format($openTickets) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#22c55e]"></span>
                            {{ $t('lawyer_dashboard.ticket_status.closed', 'التذاكر المغلقة', 'Closed Tickets') }}
                        </span>

                        <span>
                            {{ $closedPercent }}% - {{ number_format($closedTickets) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- Closed Tickets History --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ $t('lawyer_dashboard.closed_history.title', 'سجل إنجاز الإغلاق', 'Closed Tickets History') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $t('lawyer_dashboard.closed_history.subtitle', 'عدد التذاكر التي تم إغلاقها خلال آخر 7 أيام بالإضافة إلى اليوم.', 'Tickets closed during the last 7 days plus today.') }}
                </p>
            </div>

            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-black text-emerald-700">
                {{ number_format($closedTicketsHistory->sum('count')) }}
            </span>
        </div>

        <div class="mt-8 h-64">
            <div class="flex h-52 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">
                @forelse($closedTicketsHistory as $bar)
                    @php
                        $barCount = (int) data_get($bar, 'count', 0);
                        $barLabel = data_get($bar, 'label', '-');
                        $barShortDate = data_get($bar, 'short_date', '-');

                        $height = $maxClosedTicketsHistory > 0
                            ? max(6, round(($barCount / $maxClosedTicketsHistory) * 100))
                            : 6;
                    @endphp

                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-3">
                        <span class="text-xs font-bold text-[#0f1b3d]">
                            {{ $barCount }}
                        </span>

                        <div
                            class="w-full max-w-10 rounded-t-xl bg-emerald-500"
                            style="height: {{ $height }}%"
                        ></div>

                        <div class="text-center">
                            <span class="block text-[10px] text-slate-500 sm:text-xs">
                                {{ $barLabel }}
                            </span>

                            <span class="block text-[10px] text-slate-400">
                                {{ $barShortDate }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex h-full w-full items-center justify-center text-sm text-slate-500">
                        {{ $t('lawyer_dashboard.common.no_data', 'لا توجد بيانات', 'No data available') }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Latest Tickets --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 p-6">
            <div>
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ $t('lawyer_dashboard.recent_tickets.title', 'أحدث التذاكر', 'Latest Tickets') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $t('lawyer_dashboard.recent_tickets.subtitle', 'آخر التذاكر المرتبطة بالشركات والعمال المسؤول عنهم.', 'Latest tickets related to your assigned companies and workers.') }}
                </p>
            </div>

            <a
                href="{{ Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index') : '#' }}"
                class="text-sm font-semibold text-blue-700"
            >
                {{ $t('lawyer_dashboard.common.view_all', 'عرض الكل', 'View all') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.ticket_no', 'رقم التذكرة', 'Ticket No.') }}
                        </th>

                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.worker', 'العامل', 'Worker') }}
                        </th>

                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.company', 'الشركة', 'Company') }}
                        </th>

                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.title', 'عنوان التذكرة', 'Ticket Title') }}
                        </th>

                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.status', 'الحالة', 'Status') }}
                        </th>

                        <th class="px-5 py-4 text-start">
                            {{ $t('lawyer_dashboard.tickets_table.time', 'الوقت', 'Time') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTickets as $ticketItem)
                        @php
                            $ticketId = data_get($ticketItem, 'id');
                            $ticketStatusValue = data_get($ticketItem, 'status', 'open');
                            $ticketStatus = $ticketStatusMeta($ticketStatusValue);

                            $workerName = data_get($ticketItem, 'worker.name', '-');
                            $companyName = data_get($ticketItem, 'worker.company.company_name')
                                ?? data_get($ticketItem, 'company.company_name')
                                ?? data_get($ticketItem, 'company.name')
                                ?? '-';

                            $createdAt = data_get($ticketItem, 'created_at');

                            $ticketShowUrl = ($ticketId && Route::has('lawyer.tickets.show'))
                                ? route('lawyer.tickets.show', $ticketId)
                                : null;
                        @endphp

                        <tr
                            @if($ticketShowUrl)
                                onclick="window.location.href='{{ $ticketShowUrl }}'"
                            @endif
                            class="{{ $ticketShowUrl ? 'cursor-pointer' : '' }} transition hover:bg-slate-50"
                        >
                            <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                #{{ $ticketId }}
                            </td>

                            <td class="px-5 py-5 font-semibold text-[#0f1b3d]">
                                {{ $workerName }}
                            </td>

                            <td class="px-5 py-5 text-slate-600">
                                {{ $companyName }}
                            </td>

                            <td class="px-5 py-5 font-semibold text-slate-700">
                                {{ \Illuminate\Support\Str::limit($ticketTitle($ticketItem), 55) }}
                            </td>

                            <td class="px-5 py-5">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $ticketStatus['class'] }}">
                                    {{ $ticketStatus['label'] }}
                                </span>
                            </td>

                            <td class="px-5 py-5 text-slate-500">
                                {{ $createdAt ? \Illuminate\Support\Carbon::parse($createdAt)->diffForHumans() : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                {{ $t('lawyer_dashboard.recent_tickets.empty', 'لا توجد تذاكر حديثة.', 'No recent tickets.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Latest Workers + Companies --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Latest Workers --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ $t('lawyer_dashboard.workers.title', 'أحدث العمال', 'Latest Workers') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $t('lawyer_dashboard.workers.subtitle', 'آخر العمال داخل الشركات المسؤول عنها.', 'Latest workers in your assigned companies.') }}
                    </p>
                </div>

                <a
                    href="{{ Route::has('lawyer.workers.index') ? route('lawyer.workers.index') : '#' }}"
                    class="text-sm font-semibold text-blue-700"
                >
                    {{ $t('lawyer_dashboard.common.view_all', 'عرض الكل', 'View all') }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.workers_table.id', 'ID', 'ID') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.workers_table.name', 'اسم العامل', 'Worker Name') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.workers_table.company', 'الشركة', 'Company') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.workers_table.status', 'الحالة', 'Status') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentWorkers as $workerItem)
                            @php
                                $workerId = data_get($workerItem, 'id');
                                $workerName = data_get($workerItem, 'name', '-');
                                $workerStatusValue = data_get($workerItem, 'status', 'active');
                                $workerStatus = $workerStatusMeta($workerStatusValue);

                                $workerCompanyName = data_get($workerItem, 'company.company_name')
                                    ?? data_get($workerItem, 'company.name')
                                    ?? '-';

                                $workerShowUrl = ($workerId && Route::has('lawyer.workers.show'))
                                    ? route('lawyer.workers.show', $workerId)
                                    : null;
                            @endphp

                            <tr
                                @if($workerShowUrl)
                                    onclick="window.location.href='{{ $workerShowUrl }}'"
                                @endif
                                class="{{ $workerShowUrl ? 'cursor-pointer' : '' }} transition hover:bg-slate-50"
                            >
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    #{{ $workerId }}
                                </td>

                                <td class="px-5 py-5 font-semibold text-[#0f1b3d]">
                                    {{ $workerName }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ $workerCompanyName }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $workerStatus['class'] }}">
                                        {{ $workerStatus['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-500">
                                    {{ $t('lawyer_dashboard.workers.empty', 'لا يوجد عمال حتى الآن.', 'No workers yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Companies --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ $t('lawyer_dashboard.companies.title', 'أحدث الشركات', 'Latest Companies') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $t('lawyer_dashboard.companies.subtitle', 'آخر الشركات المرتبطة بحساب المحامي.', 'Latest companies assigned to the lawyer.') }}
                    </p>
                </div>

                <a
                    href="{{ Route::has('lawyer.companies.index') ? route('lawyer.companies.index') : '#' }}"
                    class="text-sm font-semibold text-blue-700"
                >
                    {{ $t('lawyer_dashboard.common.view_all', 'عرض الكل', 'View all') }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.companies_table.company', 'الشركة', 'Company') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.companies_table.workers', 'العمال', 'Workers') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.companies_table.open_tickets', 'التذاكر المفتوحة', 'Open Tickets') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ $t('lawyer_dashboard.companies_table.status', 'الحالة', 'Status') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentCompanies as $companyItem)
                            @php
                                $companyId = data_get($companyItem, 'id');
                                $companyName = data_get($companyItem, 'company_name')
                                    ?? data_get($companyItem, 'name')
                                    ?? '-';

                                $companyStatusValue = data_get($companyItem, 'status', 'active');
                                $companyStatus = $companyStatusMeta($companyStatusValue);

                                $workersCount = data_get($companyItem, 'workers_count', 0);
                                $openTicketsCount = data_get($companyItem, 'open_tickets_count', 0);

                                $companyShowUrl = ($companyId && Route::has('lawyer.companies.show'))
                                    ? route('lawyer.companies.show', $companyId)
                                    : null;
                            @endphp

                            <tr
                                @if($companyShowUrl)
                                    onclick="window.location.href='{{ $companyShowUrl }}'"
                                @endif
                                class="{{ $companyShowUrl ? 'cursor-pointer' : '' }} transition hover:bg-slate-50"
                            >
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    {{ $companyName }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ number_format((int) $workersCount) }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ number_format((int) $openTicketsCount) }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $companyStatus['class'] }}">
                                        {{ $companyStatus['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-500">
                                    {{ $t('lawyer_dashboard.companies.empty', 'لا توجد شركات حتى الآن.', 'No companies yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</div>
@endsection
