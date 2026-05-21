@extends('layouts.app')

@section('title', 'إدارة التذاكر')

@section('content')
@php
    $tickets = $tickets ?? collect();
    $statuses = [
        'open' => ['label' => 'مفتوحة', 'class' => 'bg-green-50 text-green-700', 'dot' => 'bg-green-500'],
        'pending' => ['label' => 'بانتظار الرد', 'class' => 'bg-yellow-50 text-yellow-700', 'dot' => 'bg-yellow-500'],
        'in_progress' => ['label' => 'قيد المعالجة', 'class' => 'bg-blue-50 text-blue-700', 'dot' => 'bg-blue-500'],
        'closed' => ['label' => 'مغلقة', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'],
    ];
@endphp

<div class="space-y-6 lg:space-y-8">
    <section class="space-y-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="text-start">
                <div class="text-sm text-slate-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0f1b3d]">لوحة التحكم</a>
                    <span class="mx-1">&rsaquo;</span>
                    <span class="font-bold text-[#0f1b3d]">التذاكر</span>
                </div>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">إدارة التذاكر</h1>
                <p class="mt-2 text-sm leading-7 text-slate-500">تابع كل شكاوى العمال المرتبطة بالشركات والمحامين من لوحة الإدارة.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">إجمالي التذاكر</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">التذاكر المفتوحة</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['open'] ?? 0) }}</h3>
                <p class="mt-2 text-xs font-bold text-red-600">تحتاج متابعة</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">قيد المعالجة</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['in_progress'] ?? 0) }}</h3>
                <p class="mt-2 text-xs font-bold text-slate-500">يتم العمل عليها</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">التذاكر المغلقة</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['closed'] ?? 0) }}</h3>
                <p class="mt-2 text-xs font-bold text-green-600">تم الانتهاء منها</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="border-b border-slate-100 bg-white p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="relative w-full xl:max-w-xl">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ابحث برقم التذكرة، العامل، الشركة، المحامي أو العنوان..."
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                    >
                    <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select name="company_id" class="h-12 min-w-[180px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all">كل الشركات</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->company_name }}</option>
                        @endforeach
                    </select>

                    <select name="lawyer_id" class="h-12 min-w-[180px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all">كل المحامين</option>
                        @foreach($lawyers ?? [] as $lawyer)
                            <option value="{{ $lawyer->id }}" @selected((string) request('lawyer_id') === (string) $lawyer->id)>{{ $lawyer->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all">كل الحالات</option>
                        @foreach($statuses as $key => $item)
                            <option value="{{ $key }}" @selected(request('status', 'all') === $key)>{{ $item['label'] }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                        </svg>
                        تطبيق
                    </button>

                    <a href="{{ route('admin.tickets.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">إعادة ضبط</a>
                </div>
            </div>
        </form>

        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-black text-[#0f1b3d]">قائمة التذاكر</h2>
            <p class="text-sm text-slate-500">إجمالي {{ method_exists($tickets, 'total') ? $tickets->total() : count($tickets) }} تذكرة</p>
        </div>

        <div class="hidden overflow-x-auto xl:block">
            <table class="w-full min-w-[1300px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-5 text-start font-bold">رقم التذكرة</th>
                        <th class="px-5 py-5 text-start font-bold">العنوان</th>
                        <th class="px-5 py-5 text-start font-bold">العامل</th>
                        <th class="px-5 py-5 text-start font-bold">الشركة</th>
                        <th class="px-5 py-5 text-start font-bold">المحامي</th>
                        <th class="px-5 py-5 text-start font-bold">الحالة</th>
                        <th class="px-5 py-5 text-start font-bold">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php
                            $statusData = $statuses[$ticket->status] ?? ['label' => $ticket->status ?? '-', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                            $worker = $ticket->worker;
                            $company = $ticket->company;
                            $lawyer = $ticket->lawyer;
                            $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                            $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);
                            $workerName = $worker->name ?? '-';
                            $workerInitial = mb_substr($workerName, 0, 1);
                        @endphp

                        <tr onclick="window.location.href='{{ route('admin.tickets.show', $ticket) }}'" class="cursor-pointer transition hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">{{ $ticket->id }}#</td>
                            <td class="px-5 py-5"><p class="max-w-[260px] truncate font-black text-[#0f1b3d]">{{ $displayTitle ?? '-' }}</p></td>
                            <td class="px-5 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">{{ $workerInitial }}</div>
                                    <div>
                                        <p class="font-black text-[#0f1b3d]">{{ $workerName }}</p>
                                        <p class="text-xs text-slate-400">{{ $worker->phone ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5">
                                <p class="font-bold text-[#0f1b3d]">{{ $company->company_name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $company->email ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5">
                                <p class="font-bold text-[#0f1b3d]">{{ $lawyer->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $lawyer->email ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-5">
                                <p class="font-bold text-[#0f1b3d]">{{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->created_at ? $ticket->created_at->format('H:i') : '-' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">لا توجد تذاكر حاليًا.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 xl:hidden">
            @forelse($tickets as $ticket)
                @php
                    $statusData = $statuses[$ticket->status] ?? ['label' => $ticket->status ?? '-', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                    $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                    $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);
                    $workerName = $ticket->worker?->name ?? '-';
                @endphp
                <div onclick="window.location.href='{{ route('admin.tickets.show', $ticket) }}'" class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black text-[#0f1b3d]">{{ $ticket->id }}# - {{ $displayTitle ?? '-' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $workerName }} | {{ $ticket->company?->company_name ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                        </span>
                    </div>
                    <div class="mt-4 rounded-xl bg-[#f8fbff] p-3">
                        <p class="text-xs text-slate-400">تاريخ الإنشاء</p>
                        <p class="mt-1 font-black text-[#0f1b3d]">{{ $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">لا توجد تذاكر حاليًا.</div>
            @endforelse
        </div>

        @if (method_exists($tickets, 'links') && $tickets->total() > 0)
            @php
                $tickets->appends(request()->query());
            @endphp
            <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">{{ $tickets->links() }}</div>
        @endif
    </section>
</div>
@endsection
