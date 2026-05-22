@extends('layouts.app')

@section('title', __('dashboard.page_title'))

@section('content')
@php
    $statusLabels = [
        'open' => 'مفتوحة',
        'pending' => 'بانتظار الرد',
        'in_progress' => 'قيد المعالجة',
        'closed' => 'مغلقة',
    ];

    $statusClasses = [
        'open' => 'bg-green-50 text-green-700',
        'pending' => 'bg-yellow-50 text-yellow-700',
        'in_progress' => 'bg-blue-50 text-blue-700',
        'closed' => 'bg-slate-100 text-slate-600',
    ];

    $openPercent = $ticketStatusDistribution['open_percent'];
    $closedPercent = $ticketStatusDistribution['closed_percent'];
    $totalPercent = $ticketStatusDistribution['total_percent'];
    $closedStart = min(100, $openPercent);
    $closedEnd = min(100, $openPercent + $closedPercent);
@endphp

<div class="space-y-6">

    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#0f1b3d] sm:text-3xl">نظرة عامة على النظام</h1>
            <p class="mt-2 text-sm text-slate-500">إحصائيات فعلية من قاعدة البيانات عن الشركات والمحامين والعمال والتذاكر.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card title="إجمالي الشركات" value="{{ number_format($stats['companies']) }}" type="info" icon="🏢" />
        <x-ui.stat-card title="إجمالي المحامين" value="{{ number_format($stats['lawyers']) }}" type="info" icon="⚖️" />
        <x-ui.stat-card title="إجمالي العمال" value="{{ number_format($stats['workers']) }}" type="info" icon="👷" />
        <x-ui.stat-card title="إجمالي التذاكر" value="{{ number_format($stats['tickets']) }}" type="info" icon="🎫" />
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#0f1b3d]">التذاكر بمرور الوقت</h2>
                    <p class="mt-1 text-xs text-slate-500">عدد التذاكر خلال آخر أسبوع.</p>
                </div>
            </div>

            <div class="mt-8 h-72">
                <div class="flex h-60 items-end justify-between gap-3 border-b border-slate-200 px-2 sm:gap-5">
                    @foreach($ticketsOverTime as $bar)
                        @php
                            $height = $bar['count'] > 0 ? max(10, round(($bar['count'] / $maxWeeklyTickets) * 100)) : 6;
                        @endphp
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-3">
                            <span class="text-xs font-bold text-[#0f1b3d]">{{ $bar['count'] }}</span>
                            <div
                                class="w-full max-w-10 rounded-t-xl bg-[#4f66a6]"
                                style="height: {{ $height }}%"
                                title="{{ $bar['label'] }} - {{ $bar['count'] }} تذكرة"
                            ></div>
                            <div class="text-center">
                                <span class="block text-[10px] text-slate-500 sm:text-xs">{{ $bar['label'] }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $bar['short_date'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-[#0f1b3d]">توزيع حالة القضايا</h2>
            <p class="mt-1 text-xs text-slate-500">النسبة بين إجمالي التذاكر والمفتوحة والمغلقة.</p>

            <div class="mt-8 flex flex-col items-center gap-6">
                <div
                    class="flex h-44 w-44 items-center justify-center rounded-full"
                    style="background: conic-gradient(#0f1b3d 0 {{ $closedStart }}%, #22c55e {{ $closedStart }}% {{ $closedEnd }}%, #e2e8f0 {{ $closedEnd }}% 100%)"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#0f1b3d]">{{ number_format($ticketStatusDistribution['total']) }}</span>
                        <span class="text-xs text-slate-500">إجمالي التذاكر</span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-slate-200"></span>
                            إجمالي التذاكر
                        </span>
                        <span>{{ number_format($ticketStatusDistribution['total']) }} - {{ $totalPercent }}%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#0f1b3d]"></span>
                            التذاكر المفتوحة
                        </span>
                        <span>{{ number_format($ticketStatusDistribution['open']) }} - {{ $openPercent }}%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-green-500"></span>
                            التذاكر المغلقة
                        </span>
                        <span>{{ number_format($ticketStatusDistribution['closed']) }} - {{ $closedPercent }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 p-6">
            <h2 class="text-lg font-bold text-[#0f1b3d]">آخر التذاكر</h2>
            @if(Route::has('admin.tickets.index'))
                <a href="{{ route('admin.tickets.index') }}" class="text-sm font-semibold text-blue-700">عرض الكل</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">رقم التذكرة</th>
                        <th class="px-5 py-4 text-start">العنوان</th>
                        <th class="px-5 py-4 text-start">العامل</th>
                        <th class="px-5 py-4 text-start">الشركة</th>
                        <th class="px-5 py-4 text-start">المحامي</th>
                        <th class="px-5 py-4 text-start">الحالة</th>
                        <th class="px-5 py-4 text-start">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTickets as $ticket)
                        @php
                            $title = $ticket->title_translated
                                ?: $ticket->messages->firstWhere('sender_type', 'worker')?->message_translated
                                ?: $ticket->title;
                        @endphp
                        <tr
                            @if(Route::has('admin.tickets.show'))
                                onclick="window.location.href='{{ route('admin.tickets.show', $ticket->id) }}'"
                            @endif
                            class="cursor-pointer transition hover:bg-slate-50"
                        >
                            <td class="px-5 py-5 font-bold text-[#0f1b3d]">{{ $ticket->id }}#</td>
                            <td class="max-w-[280px] truncate px-5 py-5 font-semibold text-[#0f1b3d]">{{ $title }}</td>
                            <td class="px-5 py-5">{{ $ticket->worker?->name ?? '-' }}</td>
                            <td class="px-5 py-5 text-slate-600">{{ $ticket->company?->company_name ?? '-' }}</td>
                            <td class="px-5 py-5 text-slate-600">{{ $ticket->lawyer?->name ?? '-' }}</td>
                            <td class="px-5 py-5">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$ticket->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-slate-500">{{ optional($ticket->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">لا توجد تذاكر حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <h2 class="text-lg font-bold text-[#0f1b3d]">آخر 5 شركات</h2>
                @if(Route::has('admin.companies.index'))
                    <a href="{{ route('admin.companies.index') }}" class="text-sm font-semibold text-blue-700">عرض الكل</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">ID</th>
                            <th class="px-5 py-4 text-start">الشركة</th>
                            <th class="px-5 py-4 text-start">البريد الإلكتروني</th>
                            <th class="px-5 py-4 text-start">المحامي</th>
                            <th class="px-5 py-4 text-start">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentCompanies as $company)
                            <tr
                                @if(Route::has('admin.companies.show'))
                                    onclick="window.location.href='{{ route('admin.companies.show', $company->id) }}'"
                                @endif
                                class="cursor-pointer transition hover:bg-slate-50"
                            >
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">#{{ $company->id }}</td>
                                <td class="px-5 py-5 font-semibold">{{ $company->company_name }}</td>
                                <td class="px-5 py-5 text-slate-600">{{ $company->email }}</td>
                                <td class="px-5 py-5 text-slate-600">{{ $company->lawyer?->name ?? '-' }}</td>
                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $company->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $company->status === 'active' ? 'نشط' : ($company->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">لا توجد شركات حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 p-6">
                <h2 class="text-lg font-bold text-[#0f1b3d]">آخر 5 عمال</h2>
                @if(Route::has('admin.workers.index'))
                    <a href="{{ route('admin.workers.index') }}" class="text-sm font-semibold text-blue-700">عرض الكل</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-start">ID</th>
                            <th class="px-5 py-4 text-start">العامل</th>
                            <th class="px-5 py-4 text-start">الشركة</th>
                            <th class="px-5 py-4 text-start">رقم الجوال</th>
                            <th class="px-5 py-4 text-start">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentWorkers as $worker)
                            <tr
                                @if(Route::has('admin.workers.show'))
                                    onclick="window.location.href='{{ route('admin.workers.show', $worker->id) }}'"
                                @endif
                                class="cursor-pointer transition hover:bg-slate-50"
                            >
                                <td class="px-5 py-5 font-bold text-[#0f1b3d]">#{{ $worker->id }}</td>
                                <td class="px-5 py-5 font-semibold">{{ $worker->name }}</td>
                                <td class="px-5 py-5 text-slate-600">{{ $worker->company?->company_name ?? '-' }}</td>
                                <td class="px-5 py-5 text-slate-600">{{ $worker->phone ?? '-' }}</td>
                                <td class="px-5 py-5">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $worker->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $worker->status === 'active' ? 'نشط' : ($worker->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">لا يوجد عمال حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
