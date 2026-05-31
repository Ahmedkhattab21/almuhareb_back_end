@extends('layouts.app')

@section('title', __('categories.page_title'))

@section('content')
@php
    $categories = $categories ?? collect();
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
    $statusMap = [
        'active' => ['label' => __('categories.status.active'), 'class' => 'bg-green-50 text-green-700', 'dot' => 'bg-green-500'],
        'inactive' => ['label' => __('categories.status.inactive'), 'class' => 'bg-red-50 text-red-700', 'dot' => 'bg-red-500'],
    ];
@endphp

<div class="space-y-6 lg:space-y-8">
    <section class="space-y-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="text-start">
                <div class="text-sm text-slate-500">
                    {{ __('categories.breadcrumb_parent') }}
                    <span class="mx-1">&rsaquo;</span>
                    <span class="font-bold text-[#0f1b3d]">{{ __('categories.breadcrumb_current') }}</span>
                </div>

                <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">{{ __('categories.title') }}</h1>
                <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('categories.subtitle') }}</p>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                <span>{{ __('categories.add_new') }}</span>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('categories.stats.total') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('categories.stats.active') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['active'] ?? 0) }}</h3>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('categories.stats.inactive') }}</p>
                <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">{{ number_format($stats['inactive'] ?? 0) }}</h3>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="border-b border-slate-100 bg-white p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('categories.filters.search_placeholder') }}"
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white xl:max-w-md">

                <div class="flex flex-wrap items-center gap-3">
                    <select name="status" class="h-12 min-w-[160px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="all">{{ __('categories.filters.all_statuses') }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('categories.status.active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('categories.status.inactive') }}</option>
                    </select>

                    <select name="sort" class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                        <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>{{ __('categories.filters.id_asc') }}</option>
                        <option value="latest" @selected(request('sort') === 'latest')>{{ __('categories.filters.latest') }}</option>
                        <option value="name_asc" @selected(request('sort') === 'name_asc')>{{ __('categories.filters.name_asc') }}</option>
                        <option value="name_desc" @selected(request('sort') === 'name_desc')>{{ __('categories.filters.name_desc') }}</option>
                    </select>

                    <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">{{ __('categories.filters.apply') }}</button>
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">{{ __('categories.filters.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-black text-[#0f1b3d]">{{ __('categories.table.title') }}</h2>
            <p class="text-sm text-slate-500">{{ method_exists($categories, 'total') ? $categories->total() : count($categories) }}</p>
        </div>

        <div class="hidden overflow-x-auto xl:block">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.id') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.name') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.lawyers_count') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.status') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.created_by') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.created_at') }}</th>
                        <th class="px-5 py-5 text-start font-bold">{{ __('categories.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        @php($statusData = $statusMap[$category->status] ?? ['label' => __('categories.status.unknown'), 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'])
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">#{{ $category->id }}</td>
                            <td class="px-5 py-5 font-black text-[#0f1b3d]">{{ $category->name }}</td>
                            <td class="px-5 py-5 font-bold text-slate-600">{{ number_format($category->lawyers_count ?? 0) }}</td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-5 font-bold text-slate-600">{{ $category->admin?->name ?? '-' }}</td>
                            <td class="px-5 py-5 text-slate-700">{{ $category->created_at?->format('Y-m-d h:i A') ?? '-' }}</td>
                            <td class="px-5 py-5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-[#0f1b3d] transition hover:bg-slate-50">{{ __('categories.actions.edit') }}</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('categories.actions.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-50 px-4 text-xs font-bold text-red-600 transition hover:bg-red-100">{{ __('categories.actions.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">{{ __('categories.table.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 xl:hidden">
            @forelse($categories as $category)
                @php($statusData = $statusMap[$category->status] ?? ['label' => __('categories.status.unknown'), 'class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black text-[#0f1b3d]">#{{ $category->id }} - {{ $category->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('categories.table.lawyers_count') }}: {{ number_format($category->lawyers_count ?? 0) }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>{{ $statusData['label'] }}
                        </span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex h-10 flex-1 items-center justify-center rounded-xl border border-slate-200 text-xs font-bold text-[#0f1b3d]">{{ __('categories.actions.edit') }}</a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">{{ __('categories.table.empty') }}</div>
            @endforelse
        </div>

        @if (method_exists($categories, 'links') && $categories->total() > 0)
            <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">{{ $categories->links() }}</div>
        @endif
    </section>
</div>
@endsection
