@extends('layouts.lawyer')

@section('title', __('lawyer_dashboard.page_title'))

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-[#0f1b3d] sm:text-3xl">
                {{ __('lawyer_dashboard.overview_title') }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ __('lawyer_dashboard.overview_subtitle') }}
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="#"
                class="rounded-xl bg-[#0f1b3d] px-5 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-slate-300">
                {{ __('lawyer_dashboard.actions.review_tickets') }}
            </a>

            <a href="#"
                class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700">
                {{ __('lawyer_dashboard.actions.ai_drafts') }}
            </a>
        </div>

    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.assigned_companies') }}"
            value="7"
            change="+2"
            type="success"
            icon="🏢"
        />

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.open_tickets') }}"
            value="24"
            note="{{ __('lawyer_dashboard.stats.open_tickets_hint') }}"
            type="info"
            icon="🎫"
        />

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.pending_replies') }}"
            value="8"
            note="{{ __('lawyer_dashboard.stats.pending_replies_hint') }}"
            type="danger"
            icon="✍️"
        />

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.closed_cases') }}"
            value="41"
            change="+18%"
            type="success"
            icon="✅"
        />

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.response_time') }}"
            value="{{ __('lawyer_dashboard.stats.response_time_value') }}"
            change="-12%"
            type="success"
            icon="⏱️"
        />

        <x-ui.stat-card
            title="{{ __('lawyer_dashboard.stats.rating') }}"
            value="4.9"
            note="{{ __('lawyer_dashboard.stats.rating_hint') }}"
            type="success"
            icon="⭐"
        />

    </section>

    {{-- Charts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Bar Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('lawyer_dashboard.tickets_over_time') }}
                </h2>

                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">
                    {{ __('lawyer_dashboard.last_7_days') }}
                </button>
            </div>

            <div class="mt-8 h-72">
                <div class="flex h-60 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">

                    @foreach([
                        ['day' => __('lawyer_dashboard.days.sat'), 'height' => '76%'],
                        ['day' => __('lawyer_dashboard.days.sun'), 'height' => '44%'],
                        ['day' => __('lawyer_dashboard.days.mon'), 'height' => '63%'],
                        ['day' => __('lawyer_dashboard.days.tue'), 'height' => '55%'],
                        ['day' => __('lawyer_dashboard.days.wed'), 'height' => '92%'],
                        ['day' => __('lawyer_dashboard.days.thu'), 'height' => '48%'],
                        ['day' => __('lawyer_dashboard.days.fri'), 'height' => '58%'],
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
                {{ __('lawyer_dashboard.ticket_status_chart') }}
            </h2>

            <div class="mt-8 flex flex-col items-center gap-6">

                <div
                    class="flex h-44 w-44 items-center justify-center rounded-full"
                    style="background: conic-gradient(#0f1b3d 0 50%, #4f66a6 50% 78%, #c8d4f5 78% 100%)"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#0f1b3d]">24</span>
                        <span class="text-xs text-slate-500">
                            {{ __('lawyer_dashboard.active_tickets') }}
                        </span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                            {{ __('lawyer_dashboard.ticket_status.new') }}
                        </span>
                        <span>50%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#4f66a6]"></span>
                            {{ __('lawyer_dashboard.ticket_status.in_review') }}
                        </span>
                        <span>28%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#c8d4f5]"></span>
                            {{ __('lawyer_dashboard.ticket_status.replied') }}
                        </span>
                        <span>22%</span>
                    </div>
                </div>

            </div>
        </div>

    </section>

    {{-- Tickets + Alerts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Recent Tickets --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">

            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">
                        {{ __('lawyer_dashboard.recent_tickets.title') }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('lawyer_dashboard.recent_tickets.subtitle') }}
                    </p>
                </div>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('lawyer_dashboard.common.view_all') }}
                </a>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.tickets_table.ticket_no') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.tickets_table.worker') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.tickets_table.company') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.tickets_table.status') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.tickets_table.time') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            [
                                'id' => 'TCK-2001',
                                'worker' => __('lawyer_dashboard.demo_tickets.ticket_1.worker'),
                                'company' => __('lawyer_dashboard.demo_tickets.ticket_1.company'),
                                'status' => __('lawyer_dashboard.ticket_status.new'),
                                'color' => 'red',
                                'time' => __('lawyer_dashboard.demo_tickets.ticket_1.time'),
                            ],
                            [
                                'id' => 'TCK-2002',
                                'worker' => __('lawyer_dashboard.demo_tickets.ticket_2.worker'),
                                'company' => __('lawyer_dashboard.demo_tickets.ticket_2.company'),
                                'status' => __('lawyer_dashboard.ticket_status.in_review'),
                                'color' => 'yellow',
                                'time' => __('lawyer_dashboard.demo_tickets.ticket_2.time'),
                            ],
                            [
                                'id' => 'TCK-2003',
                                'worker' => __('lawyer_dashboard.demo_tickets.ticket_3.worker'),
                                'company' => __('lawyer_dashboard.demo_tickets.ticket_3.company'),
                                'status' => __('lawyer_dashboard.ticket_status.replied'),
                                'color' => 'blue',
                                'time' => __('lawyer_dashboard.demo_tickets.ticket_3.time'),
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
                                    {{ $ticket['company'] }}
                                </td>

                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $ticket['color'] === 'red' ? 'bg-red-50 text-red-700' : '' }}
                                        {{ $ticket['color'] === 'yellow' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                        {{ $ticket['color'] === 'blue' ? 'bg-blue-50 text-blue-700' : '' }}
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

            <div class="space-y-4 p-4 md:hidden">
                @foreach([
                    [
                        'id' => 'TCK-2001',
                        'worker' => __('lawyer_dashboard.demo_tickets.ticket_1.worker'),
                        'company' => __('lawyer_dashboard.demo_tickets.ticket_1.company'),
                        'status' => __('lawyer_dashboard.ticket_status.new'),
                    ],
                    [
                        'id' => 'TCK-2002',
                        'worker' => __('lawyer_dashboard.demo_tickets.ticket_2.worker'),
                        'company' => __('lawyer_dashboard.demo_tickets.ticket_2.company'),
                        'status' => __('lawyer_dashboard.ticket_status.in_review'),
                    ],
                    [
                        'id' => 'TCK-2003',
                        'worker' => __('lawyer_dashboard.demo_tickets.ticket_3.worker'),
                        'company' => __('lawyer_dashboard.demo_tickets.ticket_3.company'),
                        'status' => __('lawyer_dashboard.ticket_status.replied'),
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
                            {{ $ticket['company'] }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Alerts --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('lawyer_dashboard.system_alerts') }}
                </h2>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('lawyer_dashboard.common.view_all') }}
                </a>
            </div>

            <div class="mt-5 space-y-4">

                <div class="rounded-2xl border-s-4 border-red-500 bg-red-50 p-4">
                    <h3 class="text-sm font-bold text-red-700">
                        {{ __('lawyer_dashboard.alerts.urgent_ticket_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('lawyer_dashboard.alerts.urgent_ticket_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-red-700">
                        {{ __('lawyer_dashboard.alerts.review_now') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-blue-500 bg-blue-50 p-4">
                    <h3 class="text-sm font-bold text-blue-700">
                        {{ __('lawyer_dashboard.alerts.ai_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('lawyer_dashboard.alerts.ai_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-blue-700">
                        {{ __('lawyer_dashboard.alerts.open_ai') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-slate-900 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('lawyer_dashboard.alerts.translation_title') }}
                    </h3>

                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('lawyer_dashboard.alerts.translation_body') }}
                    </p>

                    <a href="#" class="mt-2 inline-block text-xs font-bold text-slate-900">
                        {{ __('lawyer_dashboard.alerts.view_details') }}
                    </a>
                </div>

            </div>
        </div>

    </section>

    {{-- Assigned Companies --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-200 p-6">
            <div>
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('lawyer_dashboard.companies.title') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ __('lawyer_dashboard.companies.subtitle') }}
                </p>
            </div>

            <a href="#" class="text-sm font-semibold text-blue-700">
                {{ __('lawyer_dashboard.common.view_all') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.companies_table.company') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.companies_table.workers') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.companies_table.open_tickets') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.companies_table.last_ticket') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('lawyer_dashboard.companies_table.status') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach([
                        [
                            'company' => __('lawyer_dashboard.demo_companies.company_1.name'),
                            'workers' => '128',
                            'tickets' => '9',
                            'last_ticket' => __('lawyer_dashboard.demo_companies.company_1.last_ticket'),
                            'status' => __('lawyer_dashboard.company_status.active'),
                        ],
                        [
                            'company' => __('lawyer_dashboard.demo_companies.company_2.name'),
                            'workers' => '76',
                            'tickets' => '4',
                            'last_ticket' => __('lawyer_dashboard.demo_companies.company_2.last_ticket'),
                            'status' => __('lawyer_dashboard.company_status.active'),
                        ],
                        [
                            'company' => __('lawyer_dashboard.demo_companies.company_3.name'),
                            'workers' => '52',
                            'tickets' => '2',
                            'last_ticket' => __('lawyer_dashboard.demo_companies.company_3.last_ticket'),
                            'status' => __('lawyer_dashboard.company_status.followup'),
                        ],
                    ] as $company)
                        <tr>
                            <td class="px-5 py-5 font-bold text-[#0f1b3d]">
                                {{ $company['company'] }}
                            </td>

                            <td class="px-5 py-5 text-slate-600">
                                {{ $company['workers'] }}
                            </td>

                            <td class="px-5 py-5 text-slate-600">
                                {{ $company['tickets'] }}
                            </td>

                            <td class="px-5 py-5 text-slate-500">
                                {{ $company['last_ticket'] }}
                            </td>

                            <td class="px-5 py-5">
                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                    {{ $company['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </section>

    {{-- Timeline --}}
    <section class="rounded-2xl bg-[#0f1b3d] p-6 text-white shadow-sm">

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-xl font-bold">
                    {{ __('lawyer_dashboard.timeline_title') }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-7 text-white/60">
                    {{ __('lawyer_dashboard.timeline_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">1</div>
                    <p class="mt-2 text-xs">{{ __('lawyer_dashboard.timeline.receive') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">2</div>
                    <p class="mt-2 text-xs">{{ __('lawyer_dashboard.timeline.review') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">3</div>
                    <p class="mt-2 text-xs">{{ __('lawyer_dashboard.timeline.ai') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">✓</div>
                    <p class="mt-2 text-xs">{{ __('lawyer_dashboard.timeline.reply') }}</p>
                </div>
            </div>

        </div>

    </section>

</div>

@endsection
