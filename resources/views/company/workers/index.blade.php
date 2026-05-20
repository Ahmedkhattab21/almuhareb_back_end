@extends('layouts.company')

@section('title', __('company_workers.page_title'))

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'active' => 0,
            'pending' => 0,
            'suspended' => 0,
        ];

        $workers = $workers ?? collect();
        $nationalities = $nationalities ?? collect();
        $languages = $languages ?? collect();

        $createUrl = Route::has('company.workers.create') ? route('company.workers.create') : '#';

        $workerImageUrl = function ($worker) {
            $image = $worker->image ?? $worker->avatar ?? null;

            if (! $image) {
                return null;
            }

            if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])) {
                return $image;
            }

            return asset('storage/' . $image);
        };

        $formatLabel = function ($value) {
            if ($value instanceof \Illuminate\Database\Eloquent\Model) {
                return $value->name ?? ($value->name_ar ?? ($value->name_en ?? ($value->title ?? '-')));
            }

            if (is_array($value)) {
                return $value['name'] ??
                    ($value['name_ar'] ??
                        ($value['name_en'] ??
                            ($value['title'] ??
                                ($value['nationality'] ??
                                    ($value['prefered_language'] ?? ($value['preferred_language'] ?? '-'))))));
            }

            if (is_string($value)) {
                $decoded = json_decode($value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded['name'] ??
                        ($decoded['name_ar'] ??
                            ($decoded['name_en'] ??
                                ($decoded['title'] ??
                                    ($decoded['nationality'] ??
                                        ($decoded['prefered_language'] ?? ($decoded['preferred_language'] ?? '-'))))));
                }

                return trim($value) !== '' ? $value : '-';
            }

            return $value ?: '-';
        };

        $pick = function (array $values) use ($formatLabel) {
            foreach ($values as $value) {
                $formatted = $formatLabel($value);

                if ($formatted !== null && $formatted !== '' && $formatted !== '-') {
                    return $formatted;
                }
            }

            return '-';
        };

        $workerDisplay = function ($worker) use ($pick) {
            $raw = $worker->getAttributes();

            return [
                'name' => $worker->name ?? '-',

                'phone' => $worker->phone ?? '-',

                'email' => $worker->email ?? '-',

                'iqama' => $pick([
                    $raw['iqama_number'] ?? null,
                    $raw['residency_number'] ?? null,
                    $raw['national_id'] ?? null,
                ]),

                'position' => $pick([
                    data_get($worker, 'position.name'),
                    data_get($worker, 'position.name_ar'),
                    data_get($worker, 'position.name_en'),
                    data_get($worker, 'position.title'),
                    $raw['job_title'] ?? null,
                    $raw['position'] ?? null,
                    isset($raw['position_id']) ? '#' . $raw['position_id'] : null,
                ]),

                'nationality' => $pick([
                    data_get($worker, 'nationalityPreferredLanguage.nationality.nationality'),
                    data_get($worker, 'nationalityPreferredLanguage.nationality.name'),
                    data_get($worker, 'nationality.name'),
                    data_get($worker, 'nationality.name_ar'),
                    data_get($worker, 'nationality.name_en'),
                    $raw['nationality'] ?? null,
                    isset($raw['nationality_id']) ? '#' . $raw['nationality_id'] : null,
                ]),

                'language' => $pick([
                    data_get($worker, 'nationalityPreferredLanguage.preferedLanguage.prefered_language'),
                    data_get($worker, 'nationalityPreferredLanguage.preferredLanguage.name'),
                    data_get($worker, 'preferredLanguage.name'),
                    data_get($worker, 'language.name'),
                    $raw['preferred_language'] ?? null,
                    $raw['language'] ?? null,
                    isset($raw['preferred_language_id']) ? '#' . $raw['preferred_language_id'] : null,
                    isset($raw['language_id']) ? '#' . $raw['language_id'] : null,
                ]),

                'open_tickets' => $raw['tickets_count'] ?? ($raw['open_tickets_count'] ?? 0),
            ];
        };
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_workers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>
                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_workers.breadcrumb_current') }}
                        </span>
                    </div>

                    <h1 class="mt-2 text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                        {{ __('company_workers.title') }}
                    </h1>

                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        {{ __('company_workers.subtitle') }}
                    </p>
                </div>

                <div class="shrink-0">
                    <x-ui.button type="button" :full="false" onclick="window.location.href='{{ $createUrl }}'"
                        class="min-w-[220px] rounded-2xl text-sm font-extrabold">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21a8 8 0 0 1 16 0" />
                            <path d="M12 14v6" />
                            <path d="M9 17h6" />
                        </svg>

                        <span>{{ __('company_workers.add_new') }}</span>
                    </x-ui.button>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('company_workers.stats.total') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['total'] ?? 0) }}
                            </h3>
                        </div>

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21a8 8 0 0 1 16 0" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('company_workers.stats.active') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['active'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-green-600">
                                {{ __('company_workers.stats.active_hint') }}
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('company_workers.stats.pending') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['pending'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-yellow-600">
                                {{ __('company_workers.stats.pending_hint') }}
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 6v6l4 2" />
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-start">
                            <p class="text-sm font-medium text-slate-500">
                                {{ __('company_workers.stats.suspended') }}
                            </p>

                            <h3 class="mt-5 text-5xl font-black leading-none text-[#0f1b3d]">
                                {{ number_format($stats['suspended'] ?? 0) }}
                            </h3>

                            <p class="mt-2 text-xs font-bold text-red-600">
                                {{ __('company_workers.stats.suspended_hint') }}
                            </p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- Filters + Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">

            <form method="GET" action="{{ route('company.workers.index') }}"
                class="border-b border-slate-100 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    <div class="relative w-full xl:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('company_workers.filters.search_placeholder') }}"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-[#f8fbff] px-12 text-sm font-medium text-[#0f1b3d] outline-none transition placeholder:text-slate-400 focus:border-[#5368aa] focus:bg-white">

                        <svg class="absolute top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 start-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">

                        <select name="status"
                            class="h-12 min-w-[150px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('company_workers.filters.all_statuses') }}</option>

                            <option value="active" @selected(request('status') === 'active')>
                                {{ __('company_workers.status.active') }}
                            </option>

                            <option value="pending" @selected(request('status') === 'pending')>
                                {{ __('company_workers.status.pending') }}
                            </option>

                            <option value="suspended" @selected(request('status') === 'suspended')>
                                {{ __('company_workers.status.suspended') }}
                            </option>
                        </select>

                        <select name="nationality"
                            class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('company_workers.filters.all_nationalities') }}</option>

                            @foreach ($nationalities as $nationality)
                                <option value="{{ $nationality['value'] }}" @selected((string) request('nationality') === (string) $nationality['value'])>
                                    {{ $nationality['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <select name="language"
                            class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="all">{{ __('company_workers.filters.all_languages') }}</option>

                            @foreach ($languages as $language)
                                <option value="{{ $language['value'] }}" @selected((string) request('language') === (string) $language['value'])>
                                    {{ $language['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort"
                            class="h-12 min-w-[170px] rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-[#5368aa] focus:bg-white">
                            <option value="id_asc" @selected(request('sort', 'id_asc') === 'id_asc')>
                                {{ __('company_workers.filters.id_asc') }}
                            </option>

                            <option value="latest" @selected(request('sort') === 'latest')>
                                {{ __('company_workers.filters.latest') }}
                            </option>

                            <option value="oldest" @selected(request('sort') === 'oldest')>
                                {{ __('company_workers.filters.oldest') }}
                            </option>

                            <option value="name_asc" @selected(request('sort') === 'name_asc')>
                                {{ __('company_workers.filters.name_asc') }}
                            </option>

                            <option value="name_desc" @selected(request('sort') === 'name_desc')>
                                {{ __('company_workers.filters.name_desc') }}
                            </option>
                        </select>

                        <button type="submit"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                            </svg>

                            {{ __('company_workers.filters.apply') }}
                        </button>

                        <a href="{{ route('company.workers.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50">
                            {{ __('company_workers.filters.reset') }}
                        </a>

                    </div>
                </div>
            </form>

            <div
                class="flex flex-col gap-2 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-black text-[#0f1b3d]">
                    {{ __('company_workers.table.title') }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ __('company_workers.table.showing') }}
                    {{ method_exists($workers, 'firstItem') ? $workers->firstItem() ?? 0 : 0 }}
                    -
                    {{ method_exists($workers, 'lastItem') ? $workers->lastItem() ?? 0 : count($workers) }}
                    {{ __('company_workers.table.from') }}
                    {{ method_exists($workers, 'total') ? $workers->total() : count($workers) }}
                    {{ __('company_workers.table.worker') }}
                </p>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full min-w-[1450px] text-sm">
                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.id') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.name') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.phone') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.email') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.iqama_number') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.position') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.nationality') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.language') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.total_tickets') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.status') }}</th>
                            <th class="px-5 py-5 text-start font-bold">{{ __('company_workers.table.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($workers as $worker)
                            @php
                                $display = $workerDisplay($worker);

                                $status = $worker->status ?? 'active';

                                $statusData = [
                                    'active' => [
                                        'label' => __('company_workers.status.active'),
                                        'class' => 'bg-green-50 text-green-700',
                                        'dot' => 'bg-green-500',
                                    ],
                                    'pending' => [
                                        'label' => __('company_workers.status.pending'),
                                        'class' => 'bg-yellow-50 text-yellow-700',
                                        'dot' => 'bg-yellow-500',
                                    ],
                                    'suspended' => [
                                        'label' => __('company_workers.status.suspended'),
                                        'class' => 'bg-red-50 text-red-700',
                                        'dot' => 'bg-red-500',
                                    ],
                                ][$status] ?? [
                                    'label' => __('company_workers.status.unknown'),
                                    'class' => 'bg-slate-100 text-slate-600',
                                    'dot' => 'bg-slate-400',
                                ];

                                $initials = mb_substr($display['name'], 0, 2);

                                $imageUrl = $workerImageUrl($worker);

                                $showUrl = Route::has('company.workers.show')
                                    ? route('company.workers.show', $worker->id)
                                    : '#';
                                $editUrl = Route::has('company.workers.edit')
                                    ? route('company.workers.edit', $worker->id)
                                    : '#';
                                $destroyUrl = Route::has('company.workers.destroy')
                                    ? route('company.workers.destroy', $worker->id)
                                    : '#';
                            @endphp

                            <tr onclick="window.location.href='{{ $showUrl }}'"
                                class="cursor-pointer transition hover:bg-slate-50">

                                <td class="px-5 py-5 font-black text-[#0f1b3d]">
                                    #{{ $worker->id }}
                                </td>

                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-3">
                                        @if($imageUrl)
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $display['name'] }}"
                                                class="h-11 w-11 rounded-full border border-slate-200 object-cover shadow-sm"
                                            >
                                        @else
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                                {{ $initials }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="font-black text-[#0f1b3d]">
                                                {{ $display['name'] }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ __('company_workers.table.worker') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $display['phone'] }}
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $display['email'] }}
                                </td>

                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ $display['iqama'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $display['position'] }}
                                </td>

                                <td class="px-5 py-5 text-slate-700">
                                    {{ $display['nationality'] }}
                                </td>

                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-bold text-[#5368aa]">
                                        {{ $display['language'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ number_format($display['open_tickets']) }}
                                    </span>
                                </td>

                                <td class="px-5 py-5">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                <td class="relative px-5 py-5" onclick="event.stopPropagation()">
                                    <details class="group relative inline-block">
                                        <summary
                                            class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="12" cy="19" r="2" />
                                            </svg>
                                        </summary>

                                        <div
                                            class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                            <a href="{{ $showUrl }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                                {{ __('company_workers.actions.show') }}
                                            </a>

                                            <a href="{{ $editUrl }}"
                                                class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-[#0f1b3d]">
                                                {{ __('company_workers.actions.edit') }}
                                            </a>

                                            @if (Route::has('company.workers.destroy'))
                                                <form method="POST" action="{{ $destroyUrl }}"
                                                    onsubmit="return confirm('{{ __('company_workers.actions.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50">
                                                        {{ __('company_workers.actions.delete') }}
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-16 text-center text-slate-500">
                                    {{ __('company_workers.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="grid gap-4 p-4 xl:hidden">
                @forelse($workers as $worker)
                    @php
                        $display = $workerDisplay($worker);

                        $status = $worker->status ?? 'active';

                        $statusData = [
                            'active' => [
                                'label' => __('company_workers.status.active'),
                                'class' => 'bg-green-50 text-green-700',
                                'dot' => 'bg-green-500',
                            ],
                            'pending' => [
                                'label' => __('company_workers.status.pending'),
                                'class' => 'bg-yellow-50 text-yellow-700',
                                'dot' => 'bg-yellow-500',
                            ],
                            'suspended' => [
                                'label' => __('company_workers.status.suspended'),
                                'class' => 'bg-red-50 text-red-700',
                                'dot' => 'bg-red-500',
                            ],
                        ][$status] ?? [
                            'label' => __('company_workers.status.unknown'),
                            'class' => 'bg-slate-100 text-slate-600',
                            'dot' => 'bg-slate-400',
                        ];

                        $initials = mb_substr($display['name'], 0, 2);

                        $imageUrl = $workerImageUrl($worker);

                        $showUrl = Route::has('company.workers.show')
                            ? route('company.workers.show', $worker->id)
                            : '#';
                        $editUrl = Route::has('company.workers.edit')
                            ? route('company.workers.edit', $worker->id)
                            : '#';
                        $destroyUrl = Route::has('company.workers.destroy')
                            ? route('company.workers.destroy', $worker->id)
                            : '#';
                    @endphp

                    <div onclick="window.location.href='{{ $showUrl }}'"
                        class="cursor-pointer rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if($imageUrl)
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $display['name'] }}"
                                        class="h-12 w-12 rounded-full border border-slate-200 object-cover shadow-sm"
                                    >
                                @else
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf3ff] text-xs font-black text-[#0f1b3d]">
                                        {{ $initials }}
                                    </div>
                                @endif

                                <div>
                                    <p class="font-black text-[#0f1b3d]">
                                        #{{ $worker->id }} - {{ $display['name'] }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $display['email'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusData['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                    {{ $statusData['label'] }}
                                </span>

                                <details class="group relative inline-block">
                                    <summary
                                        class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-[#0f1b3d] [&::-webkit-details-marker]:hidden">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="12" cy="19" r="2" />
                                        </svg>
                                    </summary>

                                    <div
                                        class="absolute end-0 z-50 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                                        <a href="{{ $showUrl }}"
                                            class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                            {{ __('company_workers.actions.show') }}
                                        </a>

                                        <a href="{{ $editUrl }}"
                                            class="flex items-center gap-2 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                            {{ __('company_workers.actions.edit') }}
                                        </a>

                                        @if (Route::has('company.workers.destroy'))
                                            <form method="POST" action="{{ $destroyUrl }}"
                                                onsubmit="return confirm('{{ __('company_workers.actions.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="flex w-full items-center gap-2 px-4 py-3 text-start text-sm font-bold text-red-600 transition hover:bg-red-50">
                                                    {{ __('company_workers.actions.delete') }}
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.phone') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $display['phone'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.iqama_number') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $display['iqama'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.position') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $display['position'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.nationality') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $display['nationality'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.language') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ $display['language'] }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#f8fbff] p-3">
                                <p class="text-xs text-slate-400">
                                    {{ __('company_workers.table.total_tickets') }}
                                </p>

                                <p class="mt-1 font-black text-[#0f1b3d]">
                                    {{ number_format($display['open_tickets']) }}
                                </p>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        {{ __('company_workers.table.empty') }}
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($workers, 'links') && $workers->total() > 0)
                @php
                    $workers->appends(request()->query());
                @endphp

                <div class="border-t border-slate-100 bg-[#f7fbff] px-5 py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.pagination.showing') }}
                            <span class="text-[#0f1b3d]">{{ $workers->firstItem() ?? 0 }}</span>
                            {{ __('company_workers.pagination.to') }}
                            <span class="text-[#0f1b3d]">{{ $workers->lastItem() ?? 0 }}</span>
                            {{ __('company_workers.pagination.of') }}
                            <span class="text-[#0f1b3d]">{{ $workers->total() }}</span>
                            {{ __('company_workers.pagination.results') }}
                        </p>

                        @if ($workers->hasPages())
                            <div class="flex items-center justify-end gap-2">

                                @if ($workers->onFirstPage())
                                    <span
                                        class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('company_workers.pagination.previous') }}
                                    </span>
                                @else
                                    <a href="{{ $workers->previousPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('company_workers.pagination.previous') }}
                                    </a>
                                @endif

                                <span
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white">
                                    {{ __('company_workers.pagination.page') }}
                                    {{ $workers->currentPage() }}
                                    {{ __('company_workers.pagination.from') }}
                                    {{ $workers->lastPage() }}
                                </span>

                                @if ($workers->hasMorePages())
                                    <a href="{{ $workers->nextPageUrl() }}"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-[#0f1b3d] hover:text-white">
                                        {{ __('company_workers.pagination.next') }}
                                    </a>
                                @else
                                    <span
                                        class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-300">
                                        {{ __('company_workers.pagination.next') }}
                                    </span>
                                @endif

                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
