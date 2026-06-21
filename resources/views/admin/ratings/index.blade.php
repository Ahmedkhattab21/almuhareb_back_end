@extends('layouts.app')

@section('title', __('ratings.page_title'))

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'today' => 0, 'average' => 0, 'with_message' => 0];
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="text-sm text-slate-500">
                {{ __('ratings.breadcrumb_admin') }}
                <span class="mx-1">›</span>
                <span class="font-bold text-[#0f1b3d]">{{ __('ratings.admin_title') }}</span>
            </div>

            <h1 class="mt-2 text-3xl font-black text-[#0f1b3d]">{{ __('ratings.admin_title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('ratings.admin_subtitle') }}</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => __('ratings.total'), 'value' => number_format($stats['total'] ?? 0)],
            ['label' => __('ratings.today'), 'value' => number_format($stats['today'] ?? 0)],
            ['label' => __('ratings.average'), 'value' => number_format($stats['average'] ?? 0, 1)],
            ['label' => __('ratings.with_message'), 'value' => number_format($stats['with_message'] ?? 0)],
        ] as $card)
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                <h3 class="mt-4 text-5xl font-black text-[#0f1b3d]">{{ $card['value'] }}</h3>
            </div>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.ratings.index') }}" class="border-b border-slate-100 p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:flex-nowrap lg:items-center">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('ratings.search_placeholder') }}"
                    class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-5 text-sm font-bold outline-none focus:border-[#5368aa] lg:min-w-0 lg:flex-1"
                >

                <select name="rating" class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 lg:w-44 lg:shrink-0">
                    <option value="all">{{ __('ratings.all_ratings') }}</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>
                            {{ __('ratings.stars', ['count' => $i]) }}
                        </option>
                    @endfor
                </select>

                <select name="company_id" class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 lg:w-52 lg:shrink-0">
                    <option value="all">{{ __('ratings.all_companies') }}</option>
                    @foreach($companies ?? [] as $company)
                        <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>

                <select name="lawyer_id" class="h-12 rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 lg:w-52 lg:shrink-0">
                    <option value="all">{{ __('ratings.all_lawyers') }}</option>
                    @foreach($lawyers ?? [] as $lawyer)
                        <option value="{{ $lawyer->id }}" @selected((string) request('lawyer_id') === (string) $lawyer->id)>
                            {{ $lawyer->name }}
                        </option>
                    @endforeach
                </select>

                <button class="h-12 rounded-2xl bg-[#0f1b3d] px-7 text-sm font-extrabold text-white lg:shrink-0">
                    {{ __('ratings.apply') }}
                </button>
                <a href="{{ route('admin.ratings.index') }}" class="inline-flex h-12 items-center rounded-2xl px-4 text-sm font-bold text-blue-700 lg:shrink-0">
                    {{ __('ratings.reset') }}
                </a>
            </div>
        </form>

        <div class="border-b border-slate-100 px-5 py-5">
            <h2 class="text-2xl font-black text-[#0f1b3d]">{{ __('ratings.list_title') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.id') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.ticket') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.worker') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.company') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.lawyer') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.rating') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.message') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.date') }}</th>
                        <th class="px-5 py-4 text-start">{{ __('ratings.columns.actions') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($ratings as $rating)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">#{{ $rating->id }}</td>
                            <td class="px-5 py-5">
                                <div class="font-black text-[#0f1b3d]">#{{ $rating->ticket_id }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($rating->ticket?->title, 45) }}</div>
                            </td>
                            <td class="px-5 py-5">{{ $rating->worker?->name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $rating->company?->company_name ?? '-' }}</td>
                            <td class="px-5 py-5">{{ $rating->lawyer?->name ?? '-' }}</td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">
                                    {{ $rating->rating }} / 5
                                </span>
                            </td>
                            <td class="max-w-sm px-5 py-5 text-slate-600">
                                {{ $rating->message ? \Illuminate\Support\Str::limit($rating->message, 90) : __('ratings.no_message') }}
                            </td>
                            <td class="px-5 py-5 text-slate-500">{{ $rating->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="px-5 py-5">
                                @if($rating->ticket && Route::has('admin.tickets.show'))
                                    <a href="{{ route('admin.tickets.show', $rating->ticket) }}" class="font-bold text-blue-700 hover:underline">
                                        {{ __('ratings.view_ticket') }}
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-14 text-center text-slate-500">{{ __('ratings.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($ratings, 'links') && $ratings->hasPages())
            <div class="border-t border-slate-100 bg-[#f8fbff] px-5 py-4">
                {{ $ratings->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
