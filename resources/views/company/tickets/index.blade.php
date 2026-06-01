@extends('layouts.company')

@section('title', 'شكاوى العمال')

@section('content')
@php
    $tickets = $tickets ?? collect();

    $statuses = [
        'open' => ['label' => 'مفتوحة', 'class' => 'bg-green-50 text-green-700', 'dot' => 'bg-green-500'],
        'pending' => ['label' => 'بانتظار الرد', 'class' => 'bg-yellow-50 text-yellow-700', 'dot' => 'bg-yellow-500'],
        'in_progress' => ['label' => 'قيد المعالجة', 'class' => 'bg-blue-50 text-blue-700', 'dot' => 'bg-blue-500'],
        'closed' => ['label' => 'مغلقة', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'],
    ];

    $currentStatus = request('status', 'all');
    $currentCategory = request('category_id', 'all');
    $currentSearch = request('search');
@endphp

<div class="space-y-6 lg:space-y-8">
    <section class="space-y-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="text-start">
                <div class="text-sm text-slate-500">
                    <a href="{{ route('company.dashboard') }}" class="hover:text-[#0f1b3d]">لوحة التحكم</a>
                    <span class="mx-1">&rsaquo;</span>
                    <span class="font-bold text-[#0f1b3d]">شكاوى العمال</span>
                </div>

                <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                    شكاوى العمال
                </h1>

                <p class="mt-2 text-sm leading-7 text-slate-500">
                    تابع شكاوى العمال المرتبطة بشركتك، راجع آخر الرسائل، ورد على الطلبات المفتوحة من مكان واحد.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">إجمالي الشكاوى</p>
                        <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-green-600">كل شكاوى العمال لدى الشركة</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                            <path d="M13 6v12" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">الشكاوى المفتوحة</p>
                        <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['open'] ?? 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-red-600">تحتاج متابعة</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">قيد المعالجة</p>
                        <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['in_progress'] ?? 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-slate-500">يتم العمل عليها حاليًا</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v5l3 3" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">الشكاوى المغلقة</p>
                        <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['closed'] ?? 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-green-600">تم الانتهاء منها</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('company.tickets.index') }}" class="border-b border-slate-100 bg-white p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="relative w-full xl:max-w-xl">
                    <input
                        type="text"
                        name="search"
                        value="{{ $currentSearch }}"
                        placeholder="ابحث برقم التذكرة، اسم العامل، العنوان، أو آخر رسالة..."
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                    >
                    <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select name="status" class="h-12 min-w-[160px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all" @selected($currentStatus === 'all')>كل الحالات</option>
                        @foreach($statuses as $key => $item)
                            <option value="{{ $key }}" @selected($currentStatus === $key)>{{ $item['label'] }}</option>
                        @endforeach
                    </select>

                    <select name="category_id" class="h-12 min-w-[190px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all" @selected($currentCategory === 'all')>كل أنواع القضايا</option>
                        @foreach(($categories ?? collect()) as $category)
                            <option value="{{ $category->id }}" @selected((string) $currentCategory === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                        </svg>
                        تطبيق
                    </button>

                    <a href="{{ route('company.tickets.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                        إعادة ضبط
                    </a>
                </div>
            </div>
        </form>

        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-black text-[#0f1b3d]">قائمة شكاوى العمال</h2>
            <p class="text-sm text-slate-500">إجمالي {{ method_exists($tickets, 'total') ? $tickets->total() : count($tickets) }} شكوى</p>
        </div>

        <div class="hidden overflow-x-auto xl:block">
            <table class="w-full min-w-[1220px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-5 text-start font-bold">رقم التذكرة</th>
                        <th class="px-5 py-5 text-start font-bold">العنوان</th>
                        <th class="px-5 py-5 text-start font-bold">نوع القضية</th>
                        <th class="px-5 py-5 text-start font-bold">العامل</th>
                        <th class="px-5 py-5 text-start font-bold">المحامي</th>
                        <th class="px-5 py-5 text-start font-bold">الحالة</th>
                        <th class="px-5 py-5 text-start font-bold">آخر رسالة</th>
                        <th class="px-5 py-5 text-start font-bold">تاريخ الإنشاء</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php
                            $statusData = $statuses[$ticket->status] ?? ['label' => $ticket->status ?? '-', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                            $worker = $ticket->worker;
                            $lawyer = $ticket->lawyer;
                            $latestMessage = $ticket->latestMessage;
                            $latestMessageText = match ($latestMessage?->sender_type) {
                                'worker' => $latestMessage->message_translated ?: $latestMessage->message_original,
                                default => $latestMessage?->message_original ?: $latestMessage?->message_translated ?: $ticket->last_message_preview,
                            };
                            $latestMessagePreview = $latestMessageText
                                ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $latestMessageText)), 55)
                                : '-';
                            $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                            $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);
                            $workerName = $worker->name ?? '-';
                            $workerInitial = mb_substr($workerName, 0, 1);
                            $category = $ticket->category;
                        @endphp

                        <tr onclick="window.location.href='{{ route('company.tickets.show', $ticket) }}'" class="cursor-pointer transition hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">{{ $ticket->id }}#</td>
                            <td class="px-5 py-5">
                                <p class="max-w-[260px] truncate font-black text-[#0f1b3d]">{{ $displayTitle ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5">
                                @if($category)
                                    <span class="inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                                        {{ $category->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
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
                                <p class="font-bold text-[#0f1b3d]">{{ $lawyer->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $lawyer->email ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                    {{ $statusData['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-5">
                                <p class="w-[220px] truncate font-bold text-slate-600" title="{{ $latestMessageText ?: '-' }}">{{ $latestMessagePreview }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-400">{{ $ticket->last_message_at ? $ticket->last_message_at->diffForHumans() : '-' }}</p>
                            </td>
                            <td class="px-5 py-5">
                                <p class="font-bold text-[#0f1b3d]">{{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->created_at ? $ticket->created_at->format('H:i') : '-' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-slate-500">لا توجد شكاوى عمال حاليًا.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 xl:hidden">
            @forelse($tickets as $ticket)
                @php
                    $statusData = $statuses[$ticket->status] ?? ['label' => $ticket->status ?? '-', 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                    $worker = $ticket->worker;
                    $latestMessage = $ticket->latestMessage;
                    $latestMessageText = $latestMessage?->message_translated ?: $latestMessage?->message_original ?: $ticket->last_message_preview;
                    $latestMessagePreview = $latestMessageText
                        ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $latestMessageText)), 75)
                        : '-';
                    $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                    $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);
                    $workerName = $worker->name ?? '-';
                    $workerInitial = mb_substr($workerName, 0, 1);
                    $categoryName = $ticket->category?->name ?? '-';
                @endphp

                <div onclick="window.location.href='{{ route('company.tickets.show', $ticket) }}'" class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">{{ $workerInitial }}</div>
                            <div>
                                <p class="font-black text-[#0f1b3d]">{{ $ticket->id }}# - {{ $displayTitle ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $workerName }}</p>
                                <p class="mt-2 inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">{{ $categoryName }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>

                    <div class="mt-4 rounded-xl bg-[#f8fbff] p-3">
                        <p class="text-xs text-slate-400">آخر رسالة</p>
                        <p class="mt-1 line-clamp-2 font-black text-[#0f1b3d]" title="{{ $latestMessageText ?: '-' }}">{{ $latestMessagePreview }}</p>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">لا توجد شكاوى عمال حاليًا.</div>
            @endforelse
        </div>

        @if (method_exists($tickets, 'links') && $tickets->total() > 0)
            @php
                $tickets->appends(request()->query());
            @endphp
            <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
