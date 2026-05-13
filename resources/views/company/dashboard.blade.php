@extends('layouts.company')

@section('title', __('company_dashboard.page_title'))

@section('content')



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
            <a href="#"
                class="rounded-xl bg-[#0f1b3d] px-5 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-slate-300">
                {{ __('company_dashboard.actions.add_worker') }}
            </a>

            <a href="#"
                class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700">
                {{ __('company_dashboard.actions.new_ticket') }}
            </a>
        </div>

    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.workers') }}"
            value="128"
            change="+12%"
            type="success"
            icon="👷"
        />

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.open_tickets') }}"
            value="14"
            note="{{ __('company_dashboard.stats.open_tickets_hint') }}"
            type="info"
            icon="🎫"
        />

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.positions') }}"
            value="9"
            note="{{ __('company_dashboard.stats.positions_hint') }}"
            type="info"
            icon="💼"
        />

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.assigned_lawyer') }}"
            value="{{ __('company_dashboard.lawyer.name') }}"
            note="{{ __('company_dashboard.stats.lawyer_hint') }}"
            type="success"
            icon="⚖️"
        />

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.pending_workers') }}"
            value="6"
            note="{{ __('company_dashboard.stats.pending_workers_hint') }}"
            type="danger"
            icon="⏳"
        />

        <x-ui.stat-card
            title="{{ __('company_dashboard.stats.response_time') }}"
            value="{{ __('company_dashboard.lawyer_card.response_value') }}"
            change="-22%"
            type="success"
            icon="⏱️"
        />

    </section>

    {{-- Charts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Bar Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.tickets_over_time') }}
                </h2>

                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">
                    {{ __('company_dashboard.last_7_days') }}
                </button>
            </div>

            <div class="mt-8 h-72">
                <div class="flex h-60 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">

                    @foreach([
                        ['day' => __('company_dashboard.days.sat'), 'height' => '82%'],
                        ['day' => __('company_dashboard.days.sun'), 'height' => '48%'],
                        ['day' => __('company_dashboard.days.mon'), 'height' => '67%'],
                        ['day' => __('company_dashboard.days.tue'), 'height' => '53%'],
                        ['day' => __('company_dashboard.days.wed'), 'height' => '88%'],
                        ['day' => __('company_dashboard.days.thu'), 'height' => '40%'],
                        ['day' => __('company_dashboard.days.fri'), 'height' => '63%'],
                    ] as $bar)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-3">
                            <div
                                class="w-full max-w-10 rounded-t-xl bg-[#4f66a6]"
                                style="height: {{ $bar['height'] }}"
                            ></div>

                            <span class="text-[10px] text-slate-500 sm:text-xs">
                                {{ $bar['day'] }}
                            </span>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- Ticket Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-[#0f1b3d]">
                {{ __('company_dashboard.ticket_status_chart') }}
            </h2>

            <div class="mt-8 flex flex-col items-center gap-6">

                <div
                    class="flex h-44 w-44 items-center justify-center rounded-full"
                    style="background: conic-gradient(#0f1b3d 0 55%, #4f66a6 55% 82%, #c8d4f5 82% 100%)"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#0f1b3d]">14</span>
                        <span class="text-xs text-slate-500">
                            {{ __('company_dashboard.active_tickets') }}
                        </span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                            {{ __('company_dashboard.ticket_status.open') }}
                        </span>
                        <span>55%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#4f66a6]"></span>
                            {{ __('company_dashboard.ticket_status.pending') }}
                        </span>
                        <span>27%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#c8d4f5]"></span>
                            {{ __('company_dashboard.ticket_status.closed') }}
                        </span>
                        <span>18%</span>
                    </div>
                </div>

            </div>
        </div>

    </section>

    {{-- Tickets + Lawyer --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Recent Tickets --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">

            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ __('company_dashboard.recent_tickets.title') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('company_dashboard.recent_tickets.subtitle') }}
                    </p>
                </div>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('company_dashboard.common.view_all') }}
                </a>
            </div>

            {{-- Table Desktop / Tablet --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[720px] text-sm">
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
                        @foreach([
                            [
                                'id' => 'TCK-1001',
                                'worker' => __('company_dashboard.demo_tickets.ticket_1.worker'),
                                'title' => __('company_dashboard.demo_tickets.ticket_1.title'),
                                'status' => __('company_dashboard.ticket_status.open'),
                                'color' => 'blue',
                                'time' => __('company_dashboard.demo_tickets.ticket_1.time'),
                            ],
                            [
                                'id' => 'TCK-1002',
                                'worker' => __('company_dashboard.demo_tickets.ticket_2.worker'),
                                'title' => __('company_dashboard.demo_tickets.ticket_2.title'),
                                'status' => __('company_dashboard.ticket_status.pending'),
                                'color' => 'yellow',
                                'time' => __('company_dashboard.demo_tickets.ticket_2.time'),
                            ],
                            [
                                'id' => 'TCK-1003',
                                'worker' => __('company_dashboard.demo_tickets.ticket_3.worker'),
                                'title' => __('company_dashboard.demo_tickets.ticket_3.title'),
                                'status' => __('company_dashboard.ticket_status.closed'),
                                'color' => 'green',
                                'time' => __('company_dashboard.demo_tickets.ticket_3.time'),
                            ],
                        ] as $ticket)
                            <tr>
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    {{ $ticket['id'] }}
                                </td>

                                <td class="px-5 py-5">
                                    {{ $ticket['worker'] }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ $ticket['title'] }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $ticket['color'] === 'blue' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $ticket['color'] === 'yellow' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                        {{ $ticket['color'] === 'green' ? 'bg-green-50 text-green-700' : '' }}
                                    ">
                                        {{ $ticket['status'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5 text-slate-500">
                                    {{ $ticket['time'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="space-y-4 p-4 md:hidden">
                @foreach([
                    [
                        'id' => 'TCK-1001',
                        'worker' => __('company_dashboard.demo_tickets.ticket_1.worker'),
                        'title' => __('company_dashboard.demo_tickets.ticket_1.title'),
                        'status' => __('company_dashboard.ticket_status.open'),
                    ],
                    [
                        'id' => 'TCK-1002',
                        'worker' => __('company_dashboard.demo_tickets.ticket_2.worker'),
                        'title' => __('company_dashboard.demo_tickets.ticket_2.title'),
                        'status' => __('company_dashboard.ticket_status.pending'),
                    ],
                    [
                        'id' => 'TCK-1003',
                        'worker' => __('company_dashboard.demo_tickets.ticket_3.worker'),
                        'title' => __('company_dashboard.demo_tickets.ticket_3.title'),
                        'status' => __('company_dashboard.ticket_status.closed'),
                    ],
                ] as $ticket)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#0f1b3d]">
                                {{ $ticket['id'] }}
                            </span>

                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $ticket['status'] }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-semibold">
                            {{ $ticket['worker'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $ticket['title'] }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Assigned Lawyer --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.lawyer_card.title') }}
                </h2>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('company_dashboard.lawyer_card.view_profile') }}
                </a>
            </div>

            <div class="mt-6 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#eef3ff] text-2xl font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.lawyer.initial') }}
                </div>

                <h3 class="mt-4 text-xl font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.lawyer.name') }}
                </h3>

                <p class="mt-2 text-sm font-semibold text-slate-500">
                    {{ __('company_dashboard.lawyer.specialization') }}
                </p>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-semibold text-slate-400">
                            {{ __('company_dashboard.lawyer_card.rating') }}
                        </p>

                        <p class="mt-1 text-lg font-bold text-[#0f1b3d]">
                            4.8 / 5
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[#f8fbff] p-4">
                        <p class="text-xs font-semibold text-slate-400">
                            {{ __('company_dashboard.lawyer_card.response') }}
                        </p>

                        <p class="mt-1 text-lg font-bold text-[#0f1b3d]">
                            {{ __('company_dashboard.lawyer_card.response_value') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </section>

    {{-- Workers + Alerts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Workers --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">

            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ __('company_dashboard.workers.title') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('company_dashboard.workers.subtitle') }}
                    </p>
                </div>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('company_dashboard.common.view_all') }}
                </a>
            </div>

            {{-- Table Desktop / Tablet --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] text-sm">
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
                                {{ __('company_dashboard.workers_table.language') }}
                            </th>
                            <th class="px-5 py-4 text-start">
                                {{ __('company_dashboard.workers_table.status') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            [
                                'id' => 1,
                                'name' => __('company_dashboard.demo_workers.worker_1.name'),
                                'position' => __('company_dashboard.demo_workers.worker_1.position'),
                                'nationality' => __('company_dashboard.demo_workers.worker_1.nationality'),
                                'language' => __('company_dashboard.demo_workers.worker_1.language'),
                                'status' => __('company_dashboard.worker_status.active'),
                                'color' => 'green',
                            ],
                            [
                                'id' => 2,
                                'name' => __('company_dashboard.demo_workers.worker_2.name'),
                                'position' => __('company_dashboard.demo_workers.worker_2.position'),
                                'nationality' => __('company_dashboard.demo_workers.worker_2.nationality'),
                                'language' => __('company_dashboard.demo_workers.worker_2.language'),
                                'status' => __('company_dashboard.worker_status.active'),
                                'color' => 'green',
                            ],
                            [
                                'id' => 3,
                                'name' => __('company_dashboard.demo_workers.worker_3.name'),
                                'position' => __('company_dashboard.demo_workers.worker_3.position'),
                                'nationality' => __('company_dashboard.demo_workers.worker_3.nationality'),
                                'language' => __('company_dashboard.demo_workers.worker_3.language'),
                                'status' => __('company_dashboard.worker_status.pending'),
                                'color' => 'yellow',
                            ],
                        ] as $worker)
                            <tr>
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                    #{{ $worker['id'] }}
                                </td>

                                <td class="px-5 py-5 font-semibold text-[#0f1b3d]">
                                    {{ $worker['name'] }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ $worker['position'] }}
                                </td>

                                <td class="px-5 py-5 text-slate-600">
                                    {{ $worker['nationality'] }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $worker['language'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $worker['color'] === 'green' ? 'bg-green-50 text-green-700' : '' }}
                                        {{ $worker['color'] === 'yellow' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                    ">
                                        {{ $worker['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="space-y-4 p-4 md:hidden">
                @foreach([
                    [
                        'id' => 1,
                        'name' => __('company_dashboard.demo_workers.worker_1.name'),
                        'position' => __('company_dashboard.demo_workers.worker_1.position'),
                        'nationality' => __('company_dashboard.demo_workers.worker_1.nationality'),
                        'status' => __('company_dashboard.worker_status.active'),
                    ],
                    [
                        'id' => 2,
                        'name' => __('company_dashboard.demo_workers.worker_2.name'),
                        'position' => __('company_dashboard.demo_workers.worker_2.position'),
                        'nationality' => __('company_dashboard.demo_workers.worker_2.nationality'),
                        'status' => __('company_dashboard.worker_status.active'),
                    ],
                    [
                        'id' => 3,
                        'name' => __('company_dashboard.demo_workers.worker_3.name'),
                        'position' => __('company_dashboard.demo_workers.worker_3.position'),
                        'nationality' => __('company_dashboard.demo_workers.worker_3.nationality'),
                        'status' => __('company_dashboard.worker_status.pending'),
                    ],
                ] as $worker)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#0f1b3d]">
                                #{{ $worker['id'] }}
                            </span>

                            <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                {{ $worker['status'] }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-semibold">
                            {{ $worker['name'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $worker['position'] }} - {{ $worker['nationality'] }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Alerts --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('company_dashboard.system_alerts') }}
                </h2>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('company_dashboard.common.view_all') }}
                </a>
            </div>

            <div class="mt-5 space-y-4">

                <div class="rounded-2xl border-s-4 border-red-500 bg-red-50 p-4">
                    <h3 class="text-sm font-bold text-red-700">
                        {{ __('company_dashboard.alerts.ticket_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('company_dashboard.alerts.ticket_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-red-700">
                        {{ __('company_dashboard.alerts.view_ticket') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-blue-500 bg-blue-50 p-4">
                    <h3 class="text-sm font-bold text-blue-700">
                        {{ __('company_dashboard.alerts.workers_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('company_dashboard.alerts.workers_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-blue-700">
                        {{ __('company_dashboard.alerts.complete_update') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-slate-900 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('company_dashboard.alerts.lawyer_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('company_dashboard.alerts.lawyer_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-slate-900">
                        {{ __('company_dashboard.alerts.view_details') }}
                    </a>
                </div>

            </div>
        </div>

    </section>

    {{-- Timeline --}}
    <section class="rounded-2xl bg-[#0f1b3d] p-6 text-white shadow-sm">

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-xl font-bold">
                    {{ __('company_dashboard.timeline_title') }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-7 text-white/60">
                    {{ __('company_dashboard.timeline_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">✓</div>
                    <p class="mt-2 text-xs">
                        {{ __('company_dashboard.timeline.workers') }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">2</div>
                    <p class="mt-2 text-xs">
                        {{ __('company_dashboard.timeline.tickets') }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">3</div>
                    <p class="mt-2 text-xs">
                        {{ __('company_dashboard.timeline.lawyer') }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">↗</div>
                    <p class="mt-2 text-xs">
                        {{ __('company_dashboard.timeline.reports') }}
                    </p>
                </div>
            </div>

        </div>

    </section>

</div>

@endsection
