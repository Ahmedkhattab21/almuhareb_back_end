@php
    $items = [
        [
            'label' => __('lawyer_dashboard.sidebar.dashboard'),
            'icon' => 'dashboard',
            'active' => request()->routeIs('lawyer.dashboard'),
            'badge' => null,
            'url' => Route::has('lawyer.dashboard') ? route('lawyer.dashboard') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.assigned_companies'),
            'icon' => 'companies',
            'active' => request()->routeIs('lawyer.companies.*'),
            'badge' => null,
            'url' => Route::has('lawyer.companies.index') ? route('lawyer.companies.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.assigned_workers'),
            'icon' => 'workers',
            'active' => request()->routeIs('lawyer.workers.*'),
            'badge' => null,
            'url' => Route::has('lawyer.workers.index') ? route('lawyer.workers.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.tickets'),
            'icon' => 'tickets',
            'active' => request()->routeIs('lawyer.tickets.*'),
            'badge' => 8,
            'url' => Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.notifications'),
            'icon' => 'notifications',
            'active' => request()->routeIs('lawyer.notifications.*'),
            'badge' => 3,
            'url' => Route::has('lawyer.notifications.index') ? route('lawyer.notifications.index') : '#',
        ],
    ];

    $logoutRoute = Route::has('lawyer.logout') ? route('lawyer.logout') : '#';
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white px-2 py-2 shadow-[0_-8px_30px_rgba(15,27,61,0.08)] lg:hidden">

    <div class="grid grid-cols-6 items-center gap-1 text-center text-[10px]">

        @foreach ($items as $item)
            <a
                href="{{ $item['url'] }}"
                class="flex flex-col items-center justify-center gap-1 rounded-2xl px-1.5 py-2
                {{ $item['active'] ? 'font-semibold text-[#0f1b3d]' : 'font-medium text-slate-500' }}"
            >
                <span class="relative flex h-9 w-9 items-center justify-center rounded-xl
                    {{ $item['active'] ? 'bg-[#0f1b3d] text-white' : 'bg-slate-100 text-slate-600' }}">

                    @switch($item['icon'])

                        @case('dashboard')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 13h6V4H4v9z" />
                                <path d="M14 20h6V4h-6v16z" />
                                <path d="M4 20h6v-4H4v4z" />
                            </svg>
                        @break

                        @case('companies')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 21h16" />
                                <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                                <path d="M9 8h1" />
                                <path d="M14 8h1" />
                                <path d="M9 12h1" />
                                <path d="M14 12h1" />
                            </svg>
                        @break

                        @case('workers')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        @break

                        @case('tickets')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                                <path d="M13 6v12" />
                            </svg>
                        @break

                        @case('notifications')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                            </svg>
                        @break

                    @endswitch

                    @if (!empty($item['badge']))
                        <span class="absolute -top-1 -end-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </span>

                <span class="max-w-full truncate">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- Logout --}}
        <form method="POST" action="{{ $logoutRoute }}">
            @csrf

            <button
                type="submit"
                class="flex w-full flex-col items-center justify-center gap-1 rounded-2xl px-1.5 py-2 font-medium text-red-600"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <path d="M16 17l5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>
                </span>

                <span class="max-w-full truncate">
                    {{ __('lawyer_dashboard.sidebar.logout') }}
                </span>
            </button>
        </form>

    </div>

</nav>
