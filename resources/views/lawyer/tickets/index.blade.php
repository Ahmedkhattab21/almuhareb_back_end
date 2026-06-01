@extends('layouts.lawyer')

@section('title', __('lawyer_tickets.page_title'))

@section('content')
    @php
        $tickets = $tickets ?? collect();

        $statuses = [
            'open' => [
                'label' => __('lawyer_tickets.status.open'),
                'class' => 'bg-green-50 text-green-700',
                'dot' => 'bg-green-500',
            ],
            'pending' => [
                'label' => __('lawyer_tickets.status.pending'),
                'class' => 'bg-yellow-50 text-yellow-700',
                'dot' => 'bg-yellow-500',
            ],
            'in_progress' => [
                'label' => __('lawyer_tickets.status.in_progress'),
                'class' => 'bg-blue-50 text-blue-700',
                'dot' => 'bg-blue-500',
            ],
            'closed' => [
                'label' => __('lawyer_tickets.status.closed'),
                'class' => 'bg-slate-100 text-slate-600',
                'dot' => 'bg-slate-400',
            ],
        ];

        $currentStatus = request('status', 'all');
        $currentCompany = request('company_id', 'all');
        $currentCategory = request('category_id', 'all');
        $currentSearch = request('search');
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        <a href="{{ route('lawyer.dashboard') }}" class="hover:text-[#0f1b3d]">
                            {{ __('lawyer_tickets.breadcrumb.dashboard') }}
                        </a>
                        <span class="mx-1">&rsaquo;</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('lawyer_tickets.breadcrumb.current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('lawyer_tickets.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('lawyer_tickets.subtitle') }}
                    </p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyer_tickets.stats.total') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['total'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-green-600">
                                {{ __('lawyer_tickets.stats.total_hint') }}
                            </p>
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
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyer_tickets.stats.open') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['open'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-red-600">
                                {{ __('lawyer_tickets.stats.open_hint') }}
                            </p>
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
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyer_tickets.stats.in_progress') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['in_progress'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-slate-500">
                                {{ __('lawyer_tickets.stats.in_progress_hint') }}
                            </p>
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
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('lawyer_tickets.stats.closed') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['closed'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-green-600">
                                {{ __('lawyer_tickets.stats.closed_hint') }}
                            </p>
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

        {{-- Filters + Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('lawyer.tickets.index') }}" class="border-b border-slate-100 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div class="relative w-full xl:max-w-md">
                        <input
                            type="text"
                            name="search"
                            value="{{ $currentSearch }}"
                            placeholder="{{ __('lawyer_tickets.filters.search_placeholder') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white"
                        >

                        <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <select name="company_id" class="h-12 min-w-[220px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all" @selected($currentCompany === 'all')>
                                كل الشركات
                            </option>

                            @foreach(($companies ?? collect()) as $company)
                                <option value="{{ $company->id }}" @selected((string) $currentCompany === (string) $company->id)>
                                    {{ $company->company_name ?? $company->email }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all" @selected($currentStatus === 'all')>
                                {{ __('lawyer_tickets.filters.all_statuses') }}
                            </option>

                            @foreach($statuses as $key => $item)
                                <option value="{{ $key }}" @selected($currentStatus === $key)>
                                    {{ $item['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <select name="category_id" class="h-12 min-w-[190px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all" @selected($currentCategory === 'all')>
                                كل أنواع القضايا
                            </option>

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

                            {{ __('lawyer_tickets.filters.apply') }}
                        </button>

                        <a href="{{ route('lawyer.tickets.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                            {{ __('lawyer_tickets.filters.reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">
                    {{ __('lawyer_tickets.table.title') }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ __('lawyer_tickets.table.count', ['count' => method_exists($tickets, 'total') ? $tickets->total() : count($tickets)]) }}
                </p>
            </div>

            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full min-w-[1320px] text-sm">
                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.ticket_number') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.ticket_title') }}</th>
                            <th class="px-5 py-5 text-start font-bold">نوع القضية</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.worker') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.company') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.status') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.last_message') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('lawyer_tickets.table.created_at') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $ticket)
                            @php
                                $statusData = $statuses[$ticket->status] ?? [
                                    'label' => $ticket->status ?? '-',
                                    'class' => 'bg-slate-100 text-slate-600',
                                    'dot' => 'bg-slate-400',
                                ];

                                $worker = $ticket->worker;
                                $company = $ticket->company;
                                $category = $ticket->category;
                                $latestMessage = $ticket->latestMessage;
                                $latestMessageText = match ($latestMessage?->sender_type) {
                                    'worker' => $latestMessage->message_translated ?: $latestMessage->message_original,
                                    'lawyer' => $latestMessage->message_original,
                                    default => $latestMessage?->message_translated ?: $latestMessage?->message_original ?: $ticket->last_message_preview,
                                };
                                $latestMessagePreview = $latestMessageText
                                    ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $latestMessageText)), 55)
                                    : '-';
                                $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                                $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);

                                $workerName = $worker->name ?? '-';
                                $workerInitial = mb_substr($workerName, 0, 1);
                                $companyName = $company->company_name ?? $company->name ?? '-';
                            @endphp

                            <tr onclick="window.location.href='{{ route('lawyer.tickets.show', $ticket) }}'" class="cursor-pointer transition hover:bg-slate-50">
                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    {{ $ticket->id }}#
                                </td>

                                <td class="px-5 py-5">
                                    <p class="max-w-[230px] truncate font-black text-[#0f1b3d]">
                                        {{ $displayTitle ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-5">
                                    @if($category)
                                        <span class="inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                                            {{ $category->name }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                            {{ $workerInitial }}
                                        </div>

                                        <div>
                                            <p class="font-black text-[#0f1b3d]">
                                                {{ $workerName }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $worker->phone ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-5">
                                    <p class="font-bold text-[#0f1b3d]">
                                        {{ $companyName }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        {{ $company->email ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5">
                                    <p class="w-[220px] truncate font-bold text-slate-600" title="{{ $latestMessageText ?: '-' }}">
                                        {{ $latestMessagePreview }}
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-400">
                                        {{ $ticket->last_message_at ? $ticket->last_message_at->diffForHumans() : '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-5">
                                    <p class="font-bold text-[#0f1b3d]">
                                        {{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        {{ $ticket->created_at ? $ticket->created_at->format('H:i') : '-' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('lawyer_tickets.empty.title') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid gap-4 p-4 xl:hidden">
                @forelse($tickets as $ticket)
                    @php
                        $statusData = $statuses[$ticket->status] ?? [
                            'label' => $ticket->status ?? '-',
                            'class' => 'bg-slate-100 text-slate-600',
                            'dot' => 'bg-slate-400',
                        ];

                        $worker = $ticket->worker;
                        $company = $ticket->company;
                        $categoryName = $ticket->category?->name ?? '-';
                        $latestMessage = $ticket->latestMessage;
                        $latestMessageText = match ($latestMessage?->sender_type) {
                            'worker' => $latestMessage->message_translated ?: $latestMessage->message_original,
                            'lawyer' => $latestMessage->message_original,
                            default => $latestMessage?->message_translated ?: $latestMessage?->message_original ?: $ticket->last_message_preview,
                        };
                        $latestMessagePreview = $latestMessageText
                            ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $latestMessageText)), 75)
                            : '-';
                        $workerMessage = $ticket->messages->firstWhere('sender_type', 'worker');
                        $displayTitle = $ticket->title_translated ?: ($workerMessage?->message_translated ?: $ticket->title);

                        $workerName = $worker->name ?? '-';
                        $workerInitial = mb_substr($workerName, 0, 1);
                        $companyName = $company->company_name ?? $company->name ?? '-';
                    @endphp

                    <div onclick="window.location.href='{{ route('lawyer.tickets.show', $ticket) }}'" class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                    {{ $workerInitial }}
                                </div>

                                <div>
                                    <p class="font-black text-[#0f1b3d]">
                                        #{{ $ticket->id }} - {{ $displayTitle ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $workerName }} | {{ $companyName }}
                                    </p>

                                    <p class="mt-2 inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                                        {{ $categoryName }}
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('lawyer_tickets.table.ticket_number') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $ticket->id }}#</p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3 sm:col-span-2">
                                <p class="text-xs text-slate-400">{{ __('lawyer_tickets.table.last_message') }}</p>
                                <p class="mt-1 line-clamp-2 font-black text-[#0f1b3d]" title="{{ $latestMessageText ?: '-' }}">{{ $latestMessagePreview }}</p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">{{ __('lawyer_tickets.table.created_at') }}</p>
                                <p class="mt-1 font-black text-[#0f1b3d]">{{ $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('lawyer_tickets.empty.title') }}
                    </div>
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
