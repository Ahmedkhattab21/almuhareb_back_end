@php
    $companyUser = auth('company')->user();
    $unreadNotificationsCount =
        $companyUser && method_exists($companyUser, 'unreadNotifications')
            ? $companyUser->unreadNotifications()->count()
            : 0;

    $items = [
        [
            'label' => __('company_dashboard.sidebar.dashboard'),
            'route' => Route::has('company.dashboard')
                ? route('company.dashboard')
                : url('/company/dashboard'),
            'active' => request()->routeIs('company.dashboard'),
            'icon' => 'dashboard',
            'badge' => null,
        ],
        [
            'label' => __('company_dashboard.sidebar.workers'),
            'route' => Route::has('company.workers.index')
                ? route('company.workers.index')
                : url('/company/workers'),
            'active' => request()->routeIs('company.workers.*'),
            'icon' => 'workers',
            'badge' => null,
        ],
        [
            'label' => __('company_dashboard.sidebar.tickets'),
            'route' => Route::has('company.tickets.index')
                ? route('company.tickets.index')
                : '#',
            'active' => request()->routeIs('company.tickets.*'),
            'icon' => 'tickets',
            'badge' => null,
        ],
        [
            'label' => __('company_dashboard.sidebar.notifications'),
            'route' => Route::has('company.notifications.index')
                ? route('company.notifications.index')
                : '#',
            'active' => request()->routeIs('company.notifications.*'),
            'icon' => 'notifications',
            'badge' => $unreadNotificationsCount > 0 ? ($unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount) : null,
        ],
    ];
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white px-3 py-2 shadow-[0_-8px_30px_rgba(15,27,61,0.08)] lg:hidden">

    <div class="grid grid-cols-5 items-center gap-1 text-center text-[11px]">

        @foreach ($items as $item)
            <a
                href="{{ $item['route'] }}"
                class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 transition
                {{ $item['active'] ? 'font-semibold text-[#0f1b3d]' : 'font-medium text-slate-500' }}"
            >
                <span class="relative flex h-9 w-9 items-center justify-center rounded-xl transition
                    {{ $item['active'] ? 'bg-[#0f1b3d] text-white shadow-md shadow-slate-300' : 'bg-slate-100 text-slate-600' }}">

                    @switch($item['icon'])

                        @case('dashboard')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 11.5L12 4l9 7.5"/>
                                <path d="M5 10.5V20h14v-9.5"/>
                                <path d="M9 20v-6h6v6"/>
                            </svg>
                        @break

                        @case('workers')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21a8 8 0 0 1 16 0"/>
                                <path d="M8 8h8"/>
                                <path d="M10 4v4"/>
                                <path d="M14 4v4"/>
                            </svg>
                        @break

                        @case('tickets')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z"/>
                                <path d="M13 6v12"/>
                            </svg>
                        @break

                        @case('notifications')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                            </svg>
                        @break

                    @endswitch

                    @if (!empty($item['badge']))
                        <span class="absolute -top-1 -end-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </span>

                <span class="line-clamp-1">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- More / Open Sidebar --}}
        <button
            type="button"
            onclick="openCompanySidebar()"
            class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 font-medium text-slate-500 transition"
        >
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 18h16"/>
                </svg>
            </span>

            <span>{{ __('company_dashboard.bottom.more') }}</span>
        </button>

    </div>

</nav>
