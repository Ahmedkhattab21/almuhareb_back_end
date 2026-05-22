@php
    $items = [
        [
            'label' => __('dashboard.sidebar.dashboard'),
            'icon' => 'home',
            'active' => request()->routeIs('admin.dashboard'),
            'url' => Route::has('admin.dashboard') ? route('admin.dashboard') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.companies'),
            'icon' => 'building',
            'active' => request()->routeIs('admin.companies.*'),
            'url' => Route::has('admin.companies.index') ? route('admin.companies.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.lawyers'),
            'icon' => 'gavel',
            'active' => request()->routeIs('admin.lawyers.*'),
            'url' => Route::has('admin.lawyers.index') ? route('admin.lawyers.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.workers'),
            'icon' => 'worker',
            'active' => request()->routeIs('admin.workers.*'),
            'url' => Route::has('admin.workers.index') ? route('admin.workers.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.positions'),
            'icon' => 'briefcase',
            'active' => request()->routeIs('admin.positions.*'),
            'url' => Route::has('admin.positions.index') ? route('admin.positions.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.tickets'),
            'icon' => 'ticket',
            'active' => request()->routeIs('admin.tickets.*'),
            'url' => Route::has('admin.tickets.index') ? route('admin.tickets.index') : '#',
        ],
    ];

    $items = array_values(array_filter($items, fn ($item) => $item['url'] !== '#'));
@endphp

@if(count($items) > 0)
    <nav class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white px-2 py-2 shadow-[0_-8px_30px_rgba(15,27,61,0.08)] lg:hidden">
        <div class="grid items-center gap-1 text-center text-[10px]" style="grid-template-columns: repeat({{ count($items) }}, minmax(0, 1fr));">
            @foreach ($items as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-2xl px-1 py-2
                    {{ $item['active'] ? 'font-semibold text-[#0f1b3d]' : 'font-medium text-slate-500' }}"
                >
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl
                        {{ $item['active'] ? 'bg-[#0f1b3d] text-white' : 'bg-slate-100 text-slate-600' }}">

                        @switch($item['icon'])
                            @case('home')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 11.5L12 4l9 7.5" />
                                    <path d="M5 10.5V20h14v-9.5" />
                                    <path d="M9 20v-6h6v6" />
                                </svg>
                            @break

                            @case('building')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 21h16" />
                                    <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                                    <path d="M9 8h1" />
                                    <path d="M14 8h1" />
                                    <path d="M9 12h1" />
                                    <path d="M14 12h1" />
                                </svg>
                            @break

                            @case('gavel')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 13l-7 7" />
                                    <path d="M5 18l3 3" />
                                    <path d="M14 4l6 6" />
                                    <path d="M8 10l6 6" />
                                </svg>
                            @break

                            @case('worker')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
                                    <path d="M8 8h8" />
                                </svg>
                            @break

                            @case('briefcase')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1" />
                                    <path d="M4 7h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                                    <path d="M2 13h20" />
                                </svg>
                            @break

                            @case('ticket')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                                    <path d="M13 6v12" />
                                </svg>
                            @break
                        @endswitch
                    </span>

                    <span class="block w-full truncate leading-4">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </nav>
@endif
