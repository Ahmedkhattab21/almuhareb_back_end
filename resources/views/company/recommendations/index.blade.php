@extends('layouts.company')

@section('title', __('company_recommendations.page_title'))

@section('content')
@php
    $recommendations = $recommendations ?? collect();
    $stats = $stats ?? ['total' => 0, 'today' => 0];
@endphp

<div class="space-y-6">
    <section>
        <div class="text-sm text-slate-500">
            {{ __('company_recommendations.breadcrumb_parent') }}
            <span class="mx-1">›</span>
            <span class="font-bold text-[#0f1b3d]">{{ __('company_recommendations.title') }}</span>
        </div>

        <h1 class="mt-2 text-3xl font-black text-[#0f1b3d]">{{ __('company_recommendations.title') }}</h1>
        <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('company_recommendations.subtitle') }}</p>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-bold text-slate-500">{{ __('company_recommendations.total') }}</p>
            <h3 class="mt-4 text-5xl font-black text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
        </div>

        <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-bold text-slate-500">{{ __('company_recommendations.today') }}</p>
            <h3 class="mt-4 text-5xl font-black text-[#0f1b3d]">{{ number_format($stats['today'] ?? 0) }}</h3>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('company.recommendations.index') }}" class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('company_recommendations.search_placeholder') }}"
                    class="h-12 flex-1 rounded-2xl border border-slate-200 bg-[#f8fbff] px-5 text-sm font-bold outline-none focus:border-[#5368aa]"
                >

                <button class="h-12 rounded-2xl bg-[#0f1b3d] px-7 text-sm font-extrabold text-white">
                    {{ __('company_recommendations.apply') }}
                </button>
                <a href="{{ route('company.recommendations.index') }}" class="inline-flex h-12 items-center rounded-2xl px-4 text-sm font-bold text-blue-700">
                    {{ __('company_recommendations.reset') }}
                </a>
            </div>
        </form>

        <div class="border-b border-slate-100 px-5 py-5">
            <h2 class="text-2xl font-black text-[#0f1b3d]">{{ __('company_recommendations.list_title') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.id') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.title') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.ticket') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.worker') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.lawyer') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.category') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('company_recommendations.columns.date') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($recommendations as $recommendation)
                        <tr onclick="window.location.href='{{ route('company.recommendations.show', $recommendation) }}'" class="cursor-pointer hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">#{{ $recommendation->id }}</td>
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">{{ \Illuminate\Support\Str::limit($recommendation->title, 45) }}</td>
                            <td class="px-5 py-5">#{{ $recommendation->ticket_id }}</td>
                            <td class="px-5 py-5">{{ $recommendation->worker?->name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $recommendation->lawyer?->name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $recommendation->ticket?->category?->name ?? '-' }}</td>
                            <td class="px-5 py-5 text-slate-500">{{ $recommendation->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center text-slate-500">{{ __('company_recommendations.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($recommendations, 'links') && $recommendations->hasPages())
            <div class="border-t border-slate-100 bg-[#f8fbff] px-5 py-4">
                {{ $recommendations->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
