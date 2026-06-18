@extends('layouts.company')

@section('title', __('company_dashboard.page_title'))

@section('content')
    @php
        $company = $company ?? auth('company')->user();

        $lawyer = $lawyer ?? $company?->lawyer ?? null;

        $recentTickets = collect($recentTickets ?? []);
        $recentWorkers = collect($recentWorkers ?? []);

        $stats = $stats ?? [
            'workers' => 0,
            'tickets' => 0,
            'open_tickets' => 0,
            'closed_tickets' => 0,
            'lawyer_name' => __('company_dashboard.common.not_assigned'),
        ];

        $ticketsOverWeek = collect($ticketsOverWeek ?? []);
        $maxWeeklyTickets = max(1, (int) ($maxWeeklyTickets ?? 1));

        $ticketStatusDistribution = $ticketStatusDistribution ?? [
            'total' => 0,
            'open' => 0,
            'closed' => 0,
            'total_percent' => 0,
            'open_percent' => 0,
            'closed_percent' => 0,
        ];

        $statusMeta = function (?string $status) {
            $status = $status ?: 'open';

            return match ($status) {
                'closed', 'resolved' => [
                    'label' => __('company_dashboard.ticket_status.closed'),
                    'class' => 'bg-slate-100 text-slate-600',
                ],
                'pending', 'waiting_reply' => [
                    'label' => __('company_dashboard.ticket_status.pending'),
                    'class' => 'bg-yellow-50 text-yellow-700',
                ],
                'in_progress' => [
                    'label' => __('company_dashboard.ticket_status.in_progress'),
                    'class' => 'bg-blue-50 text-blue-700',
                ],
                default => [
                    'label' => __('company_dashboard.ticket_status.open'),
                    'class' => 'bg-green-50 text-green-700',
                ],
            };
        };

        $workerStatusMeta = function (?string $status) {
            $status = $status ?: 'active';

            return match ($status) {
                'pending' => [
                    'label' => __('company_dashboard.worker_status.pending'),
                    'class' => 'bg-yellow-50 text-yellow-700',
                ],
                'suspended', 'inactive' => [
                    'label' => __('company_dashboard.worker_status.suspended'),
                    'class' => 'bg-red-50 text-red-700',
                ],
                default => [
                    'label' => __('company_dashboard.worker_status.active'),
                    'class' => 'bg-green-50 text-green-700',
                ],
            };
        };

        $ticketTitle = function ($ticket) {
            if (app()->getLocale() === 'ar') {
                return data_get($ticket, 'title_translated')
                    ?? data_get($ticket, 'title')
                    ?? data_get($ticket, 'title_original')
                    ?? __('company_dashboard.common.no_title');
            }

            return data_get($ticket, 'title_original')
                ?? data_get($ticket, 'title')
                ?? data_get($ticket, 'title_translated')
                ?? __('company_dashboard.common.no_title');
        };

        $lawyerName = $lawyer?->name ?? __('company_dashboard.common.not_assigned');
        $lawyerInitial = mb_substr($lawyerName, 0, 1);
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#0f1b3d] sm:text-3xl">
                    {{ __('company_dashboard.overview_title') }}
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    {{ __('company_dashboard.overview_subtitle') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ Route::has('company.workers.create') ? route('company.workers.create') : '#' }}"
                    class="rounded-xl bg-[#0f1b3d] px-5 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-slate-300"
                >
                    {{ __('company_dashboard.actions.add_worker') }}
                </a>

                <a
                    href="{{ Route::has('company.tickets.index') ? route('company.tickets.index') : '#' }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700"
                >
                    {{ __('company_dashboard.actions.view_tickets') }}
                </a>
            </div>
        </section>

        {{-- Stats --}}
        <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    {{ __('company_dashboard.stats.workers') }}
                </p>

                <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                    {{ number_format($stats['workers'] ?? 0) }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    {{ __('company_dashboard.stats.total_tickets') }}
                </p>

                <p class="mt-4 text-4xl font-black text-[#0f1b3d]">
                    {{ number_format($stats['tickets'] ?? 0) }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    {{ __('company_dashboard.stats.assigned_lawyer') }}
                </p>

                <p class="mt-4 text-2xl font-black leading-9 text-[#0f1b3d]">
                    {{ $lawyerName }}
                </p>
            </div>
        </section>

        {{-- Charts --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-[#0f1b3d]">
                            {{ __('company_dashboard.tickets_over_time') }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('company_dashboard.last_7_days') }}
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
                                {{ __('company_dashboard.common.no_data') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.ticket_status_chart') }}
                </h2>

                <div class="mt-8 flex flex-col items-center gap-6">
                    <div
                        class="flex h-44 w-44 items-center justify-center rounded-full"
                        style="background: conic-gradient(#0f1b3d 0 {{ $ticketStatusDistribution['open_percent'] ?? 0 }}%, #22c55e {{ $ticketStatusDistribution['open_percent'] ?? 0 }}% {{ ($ticketStatusDistribution['open_percent'] ?? 0) + ($ticketStatusDistribution['closed_percent'] ?? 0) }}%, #e8eef8 {{ ($ticketStatusDistribution['open_percent'] ?? 0) + ($ticketStatusDistribution['closed_percent'] ?? 0) }}% 100%)"
                    >
                        <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                            <span class="text-3xl font-bold text-[#0f1b3d]">
                                {{ number_format($ticketStatusDistribution['total'] ?? 0) }}
                            </span>

                            <span class="text-xs text-slate-500">
                                {{ __('company_dashboard.ticket_status.all') }}
                            </span>
                        </div>
                    </div>

                    <div class="w-full space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-slate-200"></span>
                                {{ __('company_dashboard.ticket_status.all') }}
                            </span>

                            <span>
                                {{ $ticketStatusDistribution['total_percent'] ?? 0 }}% - {{ number_format($ticketStatusDistribution['total'] ?? 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                                {{ __('company_dashboard.ticket_status.open') }}
                            </span>

                            <span>
                                {{ $ticketStatusDistribution['open_percent'] ?? 0 }}% - {{ number_format($ticketStatusDistribution['open'] ?? 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-[#22c55e]"></span>
                                {{ __('company_dashboard.ticket_status.closed') }}
                            </span>

                            <span>
                                {{ $ticketStatusDistribution['closed_percent'] ?? 0 }}% - {{ number_format($ticketStatusDistribution['closed'] ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Recent Consultations + Consultant --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-200 p-6">
                    <div>
                        <h2 class="text-lg font-bold text-[#0f1b3d]">
                            {{ __('company_dashboard.recent_tickets.title') }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('company_dashboard.recent_tickets.subtitle') }}
                        </p>
                    </div>

                    <a
                        href="{{ Route::has('company.tickets.index') ? route('company.tickets.index') : '#' }}"
                        class="text-sm font-semibold text-blue-700"
                    >
                        {{ __('company_dashboard.common.view_all') }}
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 text-start">
                                    {{ __('company_dashboard.tickets_table.ticket_no') }}
                                </th>

                                <th class="px-5 py-4 text-start">
                                    {{ __('company_dashboard.tickets_table.worker') }}
                                </th>

                                <th class="px-5 py-4 text-start">
                                    {{ __('company_dashboard.tickets_table.title') }}
                                </th>

                                <th class="px-5 py-4 text-start">
                                    {{ __('company_dashboard.tickets_table.status') }}
                                </th>

                                <th class="px-5 py-4 text-start">
                                    {{ __('company_dashboard.tickets_table.time') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTickets as $ticketItem)
                                @php
                                    $ticketId = data_get($ticketItem, 'id');
                                    $ticketStatusValue = data_get($ticketItem, 'status', 'open');
                                    $status = $statusMeta($ticketStatusValue);

                                    $ticketWorkerName = data_get($ticketItem, 'worker.name', '-');
                                    $ticketCreatedAt = data_get($ticketItem, 'created_at');

                                    $ticketShowUrl = ($ticketId && Route::has('company.tickets.show'))
                                        ? route('company.tickets.show', $ticketId)
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
                                        {{ $ticketWorkerName }}
                                    </td>

                                    <td class="px-5 py-5 font-semibold text-slate-700">
                                        {{ \Illuminate\Support\Str::limit($ticketTitle($ticketItem), 55) }}
                                    </td>

                                    <td class="px-5 py-5">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-5 text-slate-500">
                                        {{ $ticketCreatedAt ? \Illuminate\Support\Carbon::parse($ticketCreatedAt)->diffForHumans() : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                        {{ __('company_dashboard.recent_tickets.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ __('company_dashboard.lawyer_card.title') }}
                    </h2>

                    <a
                        href="{{ Route::has('company.lawyer.show') ? route('company.lawyer.show') : '#' }}"
                        class="text-sm font-semibold text-blue-700"
                    >
                        {{ __('company_dashboard.lawyer_card.view_profile') }}
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#eef3ff] text-2xl font-bold text-[#0f1b3d]">
                        {{ $lawyerInitial }}
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-[#0f1b3d]">
                        {{ $lawyerName }}
                    </h3>

                    <p class="mt-2 text-sm font-semibold text-slate-500">
                        {{ $lawyer ? ($lawyer->email ?? '-') : __('company_dashboard.common.not_assigned') }}
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-500">
                        {{ $lawyer?->phone ?? '' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Recent Workers --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ __('company_dashboard.workers.title') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('company_dashboard.workers.subtitle') }}
                    </p>
                </div>

                <a
                    href="{{ Route::has('company.workers.index') ? route('company.workers.index') : '#' }}"
                    class="text-sm font-semibold text-blue-700"
                >
                    {{ __('company_dashboard.common.view_all') }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.id') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.name') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.position') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.nationality') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.status') }}
                            </th>

                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.created_at') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentWorkers as $workerItem)
                            @php
                                $workerId = data_get($workerItem, 'id');
                                $workerName = data_get($workerItem, 'name', '-');
                                $workerStatusValue = data_get($workerItem, 'status', 'active');

                                $position = data_get($workerItem, 'position.name')
                                    ?? data_get($workerItem, 'position')
                                    ?? data_get($workerItem, 'job_title')
                                    ?? '-';

                                $nationality = data_get($workerItem, 'nationality.nationality')
                                    ?? data_get($workerItem, 'nationality.name')
                                    ?? data_get($workerItem, 'nationality')
                                    ?? '-';

                                $createdAt = data_get($workerItem, 'created_at');

                                $workerStatus = $workerStatusMeta($workerStatusValue);

                                $workerShowUrl = ($workerId && Route::has('company.workers.show'))
                                    ? route('company.workers.show', $workerId)
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
                                    {{ is_scalar($position) ? $position : '-' }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ is_scalar($nationality) ? $nationality : '-' }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $workerStatus['class'] }}">
                                        {{ $workerStatus['label'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5 text-slate-500">
                                    {{ $createdAt ? \Illuminate\Support\Carbon::parse($createdAt)->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                    {{ __('company_dashboard.workers.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
