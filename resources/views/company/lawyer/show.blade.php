@extends('layouts.company')

@section('title', __('company_lawyer.show.page_title'))

@section('content')
    @php
        $dashboardUrl = Route::has('company.dashboard')
            ? route('company.dashboard')
            : url('/company/dashboard');
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        <a href="{{ $dashboardUrl }}" class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_lawyer.breadcrumb_parent') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_lawyer.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('company_lawyer.show.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('company_lawyer.show.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ $dashboardUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('company_lawyer.show.back') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Stats --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.assigned_lawyers') }}</p>
                <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['assigned_lawyers'] ?? 0) }}</h3>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.case_categories') }}</p>
                <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['case_categories'] ?? 0) }}</h3>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.workers') }}</p>
                <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['workers'] ?? 0) }}</h3>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('company_lawyer.show.stats.total_tickets') }}</p>
                <h3 class="mt-4 text-4xl font-black text-[#0f1b3d]">{{ number_format($stats['total_tickets'] ?? 0) }}</h3>
            </div>
        </section>

        @if($legalAssignments->isEmpty())
            {{-- Empty State --}}
            <section class="rounded-[30px] border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#eef3ff] text-[#5368aa]">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>

                <h2 class="mt-5 text-2xl font-black text-[#0f1b3d]">
                    {{ __('company_lawyer.empty.title') }}
                </h2>

                <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-500">
                    {{ __('company_lawyer.empty.subtitle') }}
                </p>
            </section>
        @else
            <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                @foreach($legalAssignments as $assignment)
                    <article class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-2xl font-black text-[#0f1b3d]">
                                {{ mb_substr($assignment['lawyer']['name'] ?? '-', 0, 1) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-400">{{ __('company_lawyer.table.name') }}</p>
                                <h2 class="mt-1 text-xl font-black text-[#0f1b3d]">
                                    {{ $assignment['lawyer']['name'] ?? '-' }}
                                </h2>

                                <div class="mt-4">
                                    <p class="text-xs font-bold text-slate-400">{{ __('company_lawyer.table.email') }}</p>
                                    <p class="mt-1 break-words text-sm font-black text-[#0f1b3d]">
                                        {{ $assignment['lawyer']['email'] ?? '-' }}
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <p class="text-xs font-bold text-slate-400">{{ __('company_lawyer.show.case_categories') }}</p>

                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @forelse($assignment['categories'] as $category)
                                            <span class="inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-extrabold text-[#5368aa]">
                                                {{ $category['name'] }}
                                            </span>
                                        @empty
                                            <span class="text-sm font-bold text-slate-500">
                                                {{ __('company_lawyer.show.no_categories') }}
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

    </div>
@endsection
