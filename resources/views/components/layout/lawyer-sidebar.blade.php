@props([
    'isRtl' => true,
])

@php
    $sideClass = $isRtl ? 'right-0' : 'left-0';
    $hiddenClass = $isRtl ? 'translate-x-full' : '-translate-x-full';

    $lawyerUser = auth('lawyer')->user();

    $lawyerUnreadNotificationsCount = 0;

    if ($lawyerUser && class_exists(\App\Models\Notifications::class)) {
        $lawyerUnreadNotificationsCount = \App\Models\Notifications::query()
            ->where('recipient_type', get_class($lawyerUser))
            ->where('recipient_id', $lawyerUser->id)
            ->whereNull('read_at')
            ->count();
    }

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
            'badge' => null,
            'url' => Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.ratings'),
            'icon' => 'ratings',
            'active' => request()->routeIs('lawyer.ratings.*'),
            'badge' => null,
            'url' => Route::has('lawyer.ratings.index') ? route('lawyer.ratings.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.recommendations'),
            'icon' => 'recommendations',
            'active' => request()->routeIs('lawyer.recommendations.*'),
            'badge' => null,
            'url' => Route::has('lawyer.recommendations.index') ? route('lawyer.recommendations.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.notifications'),
            'icon' => 'notifications',
            'active' => request()->routeIs('lawyer.notifications.*'),
            'badge' => $lawyerUnreadNotificationsCount > 0 ? $lawyerUnreadNotificationsCount : null,
            'url' => Route::has('lawyer.notifications.index') ? route('lawyer.notifications.index') : '#',
        ],
    ];
@endphp

<aside
    id="lawyerSidebar"
    class="fixed top-0 {{ $sideClass }} z-50 h-screen w-72 {{ $hiddenClass }} overflow-y-auto bg-[#0f1b3d] text-white transition-transform duration-300 lg:translate-x-0"
>
    <div class="flex min-h-full flex-col px-4 py-6">

        {{-- Logo --}}
        <div class="mb-8 flex items-center gap-3 px-2">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-[#0f1b3d] shadow-sm">
                <img src="{{ asset('brand/myaman-icon-navy.png') }}" alt="myaman" class="h-9 w-9 object-contain">
            </div>

            <div>
                <h1 class="text-xl font-bold leading-6">
                    {{ __('lawyer_dashboard.brand') }}
                </h1>

                <p class="mt-1 text-xs text-white/45">
                    {{ __('lawyer_dashboard.brand_subtitle') }}
                </p>
            </div>

            <button onclick="closeLawyerSidebar()" class="ms-auto text-white/60 lg:hidden">
                ✕
            </button>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 space-y-2">
            @foreach ($items as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="group relative flex h-12 items-center gap-3 rounded-xl px-4 text-sm transition
                    {{ $item['active']
                        ? 'bg-[#344367] text-white font-semibold'
                        : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center">
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

                            @case('ratings')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="m12 3 2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.8l-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9L12 3z" />
                                </svg>
                            @break

                            @case('recommendations')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 5h16v14H4z" />
                                    <path d="m8 11 2.5 2.5L16 8" />
                                    <path d="M8 17h8" />
                                </svg>
                            @break

                            @case('notifications')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                    <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                </svg>
                            @break
                        @endswitch
                    </span>

                    <span class="font-medium">
                        {{ $item['label'] }}
                    </span>

                    @if ($item['badge'])
                        <span class="ms-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Consultant Info --}}
        <div class="mt-8 rounded-2xl border border-white/10 bg-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/15 bg-white/10">
                    <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 13l-7 7" />
                        <path d="M5 18l3 3" />
                        <path d="M14 4l6 6" />
                    </svg>
                </div>

                <div>
                    <p class="text-sm font-bold text-white">
                        {{ __('lawyer_dashboard.sidebar.lawyer_panel') }}
                    </p>

                    <p class="mt-1 text-xs text-white/50">
                        {{ __('lawyer_dashboard.sidebar.lawyer_panel_subtitle') }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</aside>
