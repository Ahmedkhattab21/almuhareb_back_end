@extends('layouts.app')

@section('title', __('dashboard.page_title'))

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-[#0f1b3d] sm:text-3xl">
                {{ __('dashboard.overview_title') }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ __('dashboard.overview_subtitle') }}
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button class="rounded-xl bg-[#0f1b3d] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-300">
                {{ __('dashboard.audit_report') }}
            </button>

            <button class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">
                {{ __('dashboard.last_30_days') }} 📅
            </button>
        </div>

    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">

        <x-ui.stat-card title="{{ __('dashboard.stats.companies') }}" value="124" change="+49%" type="success" icon="🏢" />
        <x-ui.stat-card title="{{ __('dashboard.stats.lawyers') }}" value="450" change="+12" type="success" icon="⚖️" />
        <x-ui.stat-card title="{{ __('dashboard.stats.workers') }}" value="12,500" change="+1.2k" type="success" icon="👷" />
        <x-ui.stat-card title="{{ __('dashboard.stats.open_tickets') }}" value="84" note="{{ __('dashboard.stable') }}" type="info" icon="🎫" />
        <x-ui.stat-card title="{{ __('dashboard.stats.escalated') }}" value="12" change="20%" type="danger" icon="⚠️" />
        <x-ui.stat-card title="{{ __('dashboard.stats.response') }}" value="15 د" change="-22%" type="success" icon="⏱️" />

    </section>

    {{-- Charts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Bar Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('dashboard.tickets_over_time') }}
                </h2>

                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">
                    {{ __('dashboard.resolved') }}
                </button>
            </div>

            <div class="mt-8 h-72">
                <div class="flex h-60 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">

                    @foreach([
                        ['day' => __('dashboard.days.sat'), 'height' => '82%'],
                        ['day' => __('dashboard.days.sun'), 'height' => '48%'],
                        ['day' => __('dashboard.days.mon'), 'height' => '67%'],
                        ['day' => __('dashboard.days.tue'), 'height' => '53%'],
                        ['day' => __('dashboard.days.wed'), 'height' => '88%'],
                        ['day' => __('dashboard.days.thu'), 'height' => '40%'],
                        ['day' => __('dashboard.days.fri'), 'height' => '63%'],
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

        {{-- Donut --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-[#0f1b3d]">
                {{ __('dashboard.case_status') }}
            </h2>

            <div class="mt-8 flex flex-col items-center gap-6">

                <div
                    class="flex h-44 w-44 items-center justify-center rounded-full"
                    style="background: conic-gradient(#0f1b3d 0 65%, #4f66a6 65% 85%, #c8d4f5 85% 100%)"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#0f1b3d]">546</span>
                        <span class="text-xs text-slate-500">{{ __('dashboard.active_cases') }}</span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                            {{ __('dashboard.pending') }}
                        </span>
                        <span>15%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#4f66a6]"></span>
                            {{ __('dashboard.in_progress') }}
                        </span>
                        <span>65%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#c8d4f5]"></span>
                            {{ __('dashboard.archived') }}
                        </span>
                        <span>20%</span>
                    </div>
                </div>

            </div>
        </div>

    </section>

    {{-- Tickets + Alerts --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Tickets --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">

            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('dashboard.recent_tickets') }}
                </h2>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('dashboard.view_all_tickets') }}
                </a>
            </div>

            {{-- Table Desktop / Tablet --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[720px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">{{ __('dashboard.table.ticket_no') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('dashboard.table.worker') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('dashboard.table.company') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('dashboard.table.status') }}</th>
                            <th class="px-5 py-4 text-start">{{ __('dashboard.table.date') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            ['id' => '#TK-9021', 'name' => 'أحمد منصور', 'company' => 'مجموعة مكة لوجستيات', 'status' => __('dashboard.status.replied'), 'color' => 'blue', 'date' => '24 أكتوبر 2023'],
                            ['id' => '#TK-8954', 'name' => 'راكان الفارس', 'company' => 'مركز جوال تك', 'status' => __('dashboard.status.processing'), 'color' => 'yellow', 'date' => '23 أكتوبر 2023'],
                            ['id' => '#TK-8942', 'name' => 'سارة طارق', 'company' => 'المدينة للمقاولات', 'status' => __('dashboard.status.new'), 'color' => 'red', 'date' => '23 أكتوبر 2023'],
                        ] as $ticket)
                            <tr>
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">{{ $ticket['id'] }}</td>
                                <td class="px-5 py-5">{{ $ticket['name'] }}</td>
                                <td class="px-5 py-5 text-slate-600">{{ $ticket['company'] }}</td>
                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $ticket['color'] === 'blue' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $ticket['color'] === 'yellow' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                        {{ $ticket['color'] === 'red' ? 'bg-red-50 text-red-700' : '' }}
                                    ">
                                        {{ $ticket['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 text-slate-500">{{ $ticket['date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="space-y-4 p-4 md:hidden">
                @foreach([
                    ['id' => '#TK-9021', 'name' => 'أحمد منصور', 'company' => 'مجموعة مكة لوجستيات', 'status' => __('dashboard.status.replied')],
                    ['id' => '#TK-8954', 'name' => 'راكان الفارس', 'company' => 'مركز جوال تك', 'status' => __('dashboard.status.processing')],
                    ['id' => '#TK-8942', 'name' => 'سارة طارق', 'company' => 'المدينة للمقاولات', 'status' => __('dashboard.status.new')],
                ] as $ticket)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#0f1b3d]">{{ $ticket['id'] }}</span>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $ticket['status'] }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-semibold">{{ $ticket['name'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $ticket['company'] }}</p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Alerts --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#0f1b3d]">
                    {{ __('dashboard.system_alerts') }}
                </h2>

                <a href="#" class="text-sm font-semibold text-blue-700">
                    {{ __('dashboard.view_all') }}
                </a>
            </div>

            <div class="mt-5 space-y-4">

                <div class="rounded-2xl border-s-4 border-red-500 bg-red-50 p-4">
                    <h3 class="text-sm font-bold text-red-700">
                        {{ __('dashboard.alerts.compliance_title') }}
                    </h3>
                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('dashboard.alerts.compliance_body') }}
                    </p>
                    <a href="#" class="mt-2 inline-block text-xs font-bold text-red-700">
                        {{ __('dashboard.alerts.action_now') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-blue-500 bg-blue-50 p-4">
                    <h3 class="text-sm font-bold text-blue-700">
                        {{ __('dashboard.alerts.workers_title') }}
                    </h3>
                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('dashboard.alerts.workers_body') }}
                    </p>
                    <a href="#" class="mt-2 inline-block text-xs font-bold text-blue-700">
                        {{ __('dashboard.alerts.complete_update') }}
                    </a>
                </div>

                <div class="rounded-2xl border-s-4 border-slate-900 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('dashboard.alerts.system_title') }}
                    </h3>
                    <p class="mt-2 text-xs leading-6 text-slate-600">
                        {{ __('dashboard.alerts.system_body') }}
                    </p>
                    <a href="#" class="mt-2 inline-block text-xs font-bold text-slate-900">
                        {{ __('dashboard.alerts.view_details') }}
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
                    {{ __('dashboard.timeline_title') }}
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-7 text-white/60">
                    {{ __('dashboard.timeline_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">✓</div>
                    <p class="mt-2 text-xs">{{ __('dashboard.timeline.mobile') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">2</div>
                    <p class="mt-2 text-xs">{{ __('dashboard.timeline.gov') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">3</div>
                    <p class="mt-2 text-xs">{{ __('dashboard.timeline.reports') }}</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white/15">↗</div>
                    <p class="mt-2 text-xs">{{ __('dashboard.timeline.ai') }}</p>
                </div>
            </div>

        </div>

    </section>

</div>

@endsection
