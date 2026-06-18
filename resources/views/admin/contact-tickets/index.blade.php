@extends('layouts.app')

@section('title', __('contact_tickets.page_title'))

@section('content')
@php
    $tickets = $tickets ?? collect();
    $statuses = [
        'new' => ['label' => __('contact_tickets.statuses.new'), 'class' => 'bg-blue-50 text-blue-700', 'dot' => 'bg-blue-500'],
        'read' => ['label' => __('contact_tickets.statuses.read'), 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'],
    ];
@endphp

<div class="space-y-6 lg:space-y-8">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="text-start">
                <div class="text-sm text-slate-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0f1b3d]">{{ __('dashboard.sidebar.dashboard') }}</a>
                    <span class="mx-1">&rsaquo;</span>
                    <span class="font-bold text-[#0f1b3d]">{{ __('contact_tickets.title') }}</span>
                </div>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">{{ __('contact_tickets.title') }}</h1>
                <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('contact_tickets.subtitle') }}</p>
            </div>
            <x-admin.report-actions report="contact-tickets" />
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('contact_tickets.stats.total') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('contact_tickets.stats.new') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['new'] ?? 0) }}</h3>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('contact_tickets.stats.read') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['read'] ?? 0) }}</h3>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.contact-tickets.index') }}" class="border-b border-slate-100 bg-white p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('contact_tickets.filters.search') }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white xl:max-w-xl"
                >

                <div class="flex flex-wrap items-center gap-3">
                    <select name="status" class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all">{{ __('contact_tickets.filters.all_statuses') }}</option>
                        @foreach($statuses as $key => $item)
                            <option value="{{ $key }}" @selected(request('status', 'all') === $key)>{{ $item['label'] }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                        {{ __('contact_tickets.filters.apply') }}
                    </button>

                    <a href="{{ route('admin.contact-tickets.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">{{ __('contact_tickets.filters.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="hidden overflow-x-auto xl:block">
            <table class="w-full min-w-[1000px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-5 text-start font-bold">{{ __('contact_tickets.table.sender') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('contact_tickets.table.company') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('contact_tickets.table.message') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('contact_tickets.table.status') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('contact_tickets.table.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php
                            $statusData = $statuses[$ticket->status] ?? $statuses['new'];
                        @endphp
                        <tr onclick="window.location.href='{{ route('admin.contact-tickets.show', $ticket) }}'" class="cursor-pointer transition hover:bg-slate-50">
                            <td class="px-5 py-5">
                                <p class="font-black text-[#0f1b3d]">{{ $ticket->name }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->email }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->phone ?: '-' }}</p>
                            </td>
                            <td class="px-5 py-5 font-bold text-[#0f1b3d]">{{ $ticket->company ?: '-' }}</td>
                            <td class="px-5 py-5">
                                <p class="max-w-[420px] truncate font-bold text-slate-600">{{ $ticket->message }}</p>
                            </td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-5">
                                <p class="font-bold text-[#0f1b3d]">{{ $ticket->created_at?->format('Y-m-d') ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->created_at?->format('H:i') ?? '-' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-500">{{ __('contact_tickets.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 xl:hidden">
            @forelse($tickets as $ticket)
                @php
                    $statusData = $statuses[$ticket->status] ?? $statuses['new'];
                @endphp
                <div onclick="window.location.href='{{ route('admin.contact-tickets.show', $ticket) }}'" class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black text-[#0f1b3d]">{{ $ticket->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $ticket->email }} | {{ $ticket->phone ?: '-' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                        </span>
                    </div>
                    <p class="mt-4 line-clamp-3 text-sm font-bold leading-7 text-slate-600">{{ $ticket->message }}</p>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">{{ __('contact_tickets.empty') }}</div>
            @endforelse
        </div>

        @if (method_exists($tickets, 'links') && $tickets->total() > 0)
            <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">{{ $tickets->links() }}</div>
        @endif
    </section>
</div>
@endsection
