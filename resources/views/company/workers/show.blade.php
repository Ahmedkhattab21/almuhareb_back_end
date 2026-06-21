@extends('layouts.company')

@section('title', __('company_workers.show.page_title'))

@section('content')
    @php
        $raw = $worker->getAttributes();

        $pick = function (array $values) {
            foreach ($values as $value) {
                if ($value !== null && $value !== '') {
                    return $value;
                }
            }

            return '-';
        };

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

        $workerName = $worker->name ?? '-';

        $nationalityLabel = $pick([
            data_get($worker, 'nationalityPreferredLanguage.nationality.nationality'),
            data_get($worker, 'nationalityPreferredLanguage.nationality.name'),
            data_get($worker, 'nationality.name'),
            $raw['nationality'] ?? null,
            isset($raw['nationality_id']) ? '#' . $raw['nationality_id'] : null,
        ]);

        $languageLabel = $pick([
            data_get($worker, 'nationalityPreferredLanguage.preferedLanguage.prefered_language'),
            data_get($worker, 'nationalityPreferredLanguage.preferredLanguage.name'),
            data_get($worker, 'preferredLanguage.name'),
            $raw['preferred_language'] ?? null,
            $raw['language'] ?? null,
            isset($raw['preferred_language_id']) ? '#' . $raw['preferred_language_id'] : null,
            isset($raw['language_id']) ? '#' . $raw['language_id'] : null,
        ]);

        $positionLabel = $pick([
            data_get($worker, 'position.name'),
            $raw['position'] ?? null,
            $raw['job_title'] ?? null,
            isset($raw['position_id']) ? '#' . $raw['position_id'] : null,
        ]);

        $companyName = $pick([
            data_get($worker, 'company.company_name'),
            data_get($worker, 'company.name'),
            auth('company')->user()->company_name ?? null,
            auth('company')->user()->name ?? null,
        ]);

        $iqamaNumber = $pick([
            $raw['iqama_number'] ?? null,
            $raw['residency_number'] ?? null,
            $raw['national_id'] ?? null,
        ]);

        $image = $raw['image'] ?? $raw['avatar'] ?? null;

        $imageUrl = !empty($image)
            ? asset('storage/' . $image)
            : null;

        $indexUrl = Route::has('company.workers.index')
            ? route('company.workers.index')
            : url('/company/workers');

        $editUrl = Route::has('company.workers.edit')
            ? route('company.workers.edit', $worker->id)
            : '#';

        $ticketsCount = $worker->tickets_count ?? 0;
    @endphp

    <div class="space-y-6 lg:space-y-8">

        {{-- Header --}}
        <section class="space-y-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="text-start">
                    <div class="text-sm text-slate-500">
                        {{ __('company_workers.breadcrumb_parent') }}
                        <span class="mx-1">›</span>

                        <a href="{{ $indexUrl }}"
                            class="font-bold text-slate-500 transition hover:text-[#0f1b3d]">
                            {{ __('company_workers.breadcrumb_current') }}
                        </a>

                        <span class="mx-1">›</span>

                        <span class="font-bold text-[#0f1b3d]">
                            {{ __('company_workers.show.breadcrumb_current') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-[#0f1b3d] sm:text-4xl">
                                {{ __('company_workers.show.title') }}
                            </h1>

                            <p class="mt-2 text-sm leading-7 text-slate-500">
                                {{ __('company_workers.show.subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ $indexUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-extrabold text-[#0f1b3d] shadow-sm transition hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>

                        {{ __('company_workers.show.back') }}
                    </a>

                    <a href="{{ $editUrl }}"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-6 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                        </svg>

                        {{ __('company_workers.actions.edit') }}
                    </a>
                </div>

            </div>
        </section>

        {{-- Profile Card --}}
        <section class="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-[180px_1fr_260px] lg:items-center">

                {{-- Image --}}
                <div class="flex justify-center lg:justify-start">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $workerName }}"
                            class="h-36 w-36 rounded-[32px] border border-slate-200 object-cover shadow-sm">
                    @else
                        <div class="flex h-36 w-36 items-center justify-center rounded-[32px] bg-[#edf3ff] text-6xl font-black text-[#0f1b3d]">
                            {{ mb_substr($workerName, 0, 1) }}
                        </div>
                    @endif
                </div>

                {{-- Main Info --}}
                <div class="text-center lg:text-start">
                    <div class="flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                        <h2 class="text-3xl font-black text-[#0f1b3d]">
                            {{ $workerName }}
                        </h2>

                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm font-bold text-slate-500">
                        {{ $positionLabel }}
                    </p>

                    <div class="mt-5 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 xl:grid-cols-3">

                        <div class="flex items-center justify-center gap-2 rounded-2xl bg-[#f8fbff] px-4 py-3 lg:justify-start">
                            <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.86 19.86 0 0 1 3.08 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.61a2 2 0 0 1-.45 2.11L9 10.64a16 16 0 0 0 4.36 4.36l1.2-1.18a2 2 0 0 1 2.11-.45c.84.3 1.71.51 2.61.63A2 2 0 0 1 22 16.92Z" />
                            </svg>

                            <span class="font-extrabold text-[#0f1b3d]">
                                {{ $worker->phone ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-center gap-2 rounded-2xl bg-[#f8fbff] px-4 py-3 lg:justify-start">
                            <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M4 4h16v16H4z" />
                                <path d="M22 6l-10 7L2 6" />
                            </svg>

                            <span class="font-extrabold text-[#0f1b3d]">
                                {{ $worker->email ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-center gap-2 rounded-2xl bg-[#f8fbff] px-4 py-3 lg:justify-start">
                            <svg class="h-5 w-5 text-[#5368aa]" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4 8 4v14" />
                                <path d="M9 21v-8h8v8" />
                            </svg>

                            <span class="font-extrabold text-[#0f1b3d]">
                                {{ $companyName }}
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Quick Status --}}
                <div class="rounded-[26px] border border-slate-200 bg-[#f8fbff] p-5 text-center">
                    <p class="text-sm font-bold text-slate-500">
                        {{ __('company_workers.show.worker_id') }}
                    </p>

                    <h3 class="mt-3 text-4xl font-black text-[#0f1b3d]">
                        #{{ $worker->id }}
                    </h3>

                    <div class="mt-5 border-t border-slate-200 pt-4">
                        <p class="text-xs font-bold text-slate-400">
                            {{ __('company_workers.show.created_at') }}
                        </p>

                        <p class="mt-1 text-sm font-black text-[#0f1b3d]">
                            {{ $worker->created_at ? $worker->created_at->format('Y-m-d h:i A') : '-' }}
                        </p>
                    </div>
                </div>

            </div>
        </section>

        {{-- Stats Cards --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.show.company') }}
                        </p>

                        <h3 class="mt-4 text-xl font-black text-[#0f1b3d]">
                            {{ $companyName }}
                        </h3>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#5368aa]">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4 8 4v14" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.table.nationality') }}
                        </p>

                        <h3 class="mt-4 text-2xl font-black text-[#0f1b3d]">
                            {{ $nationalityLabel }}
                        </h3>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M2 12h20" />
                            <path d="M12 2a15.3 15.3 0 0 1 0 20" />
                            <path d="M12 2a15.3 15.3 0 0 0 0 20" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.table.language') }}
                        </p>

                        <h3 class="mt-4 text-2xl font-black text-[#0f1b3d]">
                            {{ $languageLabel }}
                        </h3>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.table.status') }}
                        </p>

                        <div class="mt-4">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-extrabold {{ $statusData['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                {{ $statusData['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            {{ __('company_workers.table.total_tickets') }}
                        </p>

                        <h3 class="mt-4 text-3xl font-black text-[#0f1b3d]">
                            {{ number_format($ticketsCount) }}
                        </h3>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-700">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                            <path d="M13 6v12" />
                        </svg>
                    </div>
                </div>
            </div>

        </section>

        {{-- Details --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- Personal Details --}}
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm xl:col-span-1">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('company_workers.show.sections.personal') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.name') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $workerName }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.phone') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $worker->phone ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.email') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $worker->email ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.iqama_number') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $iqamaNumber }}</span>
                    </div>
                </div>
            </div>

            {{-- Work Details --}}
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm xl:col-span-1">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('company_workers.show.sections.work') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.company') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $companyName }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.operating_company') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $worker->operating_company ?: '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.position') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $positionLabel }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.nationality') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $nationalityLabel }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.language') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">{{ $languageLabel }}</span>
                    </div>
                </div>
            </div>

            {{-- System Details --}}
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm xl:col-span-1">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
                    <h2 class="text-lg font-black text-[#0f1b3d]">
                        {{ __('company_workers.show.sections.system') }}
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 p-5">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.show.worker_id') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">#{{ $worker->id }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.table.total_tickets') }}</span>
                        <span class="inline-flex rounded-full bg-purple-50 px-3 py-1 text-xs font-bold text-purple-700">
                            {{ number_format($ticketsCount) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.form.status') }}</span>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold {{ $statusData['class'] }}">
                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                            {{ $statusData['label'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.show.created_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $worker->created_at ? $worker->created_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-sm font-bold text-slate-500">{{ __('company_workers.show.updated_at') }}</span>
                        <span class="text-sm font-black text-[#0f1b3d]">
                            {{ $worker->updated_at ? $worker->updated_at->format('Y-m-d h:i A') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </section>

        {{-- Bottom Actions --}}
        <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-end gap-3">

                <a href="{{ $indexUrl }}"
                    class="inline-flex h-12 w-[145px] flex-none items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-[#0f1b3d] transition hover:bg-slate-50">
                    {{ __('company_workers.show.back') }}
                </a>

                <a href="{{ $editUrl }}"
                    class="inline-flex h-12 w-[145px] flex-none items-center justify-center gap-2 rounded-2xl bg-[#0f1b3d] px-4 text-sm font-extrabold text-white shadow-md transition hover:bg-[#16264f]">
                    {{ __('company_workers.actions.edit') }}
                </a>

            </div>
        </section>

    </div>
@endsection
