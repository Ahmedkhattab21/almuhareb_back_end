@props([
    'isRtl' => true,
])

@php
    $sideClass = $isRtl ? 'right-0' : 'left-0';
    $hiddenClass = $isRtl ? 'translate-x-full' : '-translate-x-full';

    $items = [
        [
            'label' => __('lawyer_dashboard.sidebar.dashboard'),
            'icon' => 'dashboard',
            'active' => request()->routeIs('lawyer.dashboard'),
            'badge' => null,
            'url' => Route::has('lawyer.dashboard') ? route('lawyer.dashboard') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.tickets'),
            'icon' => 'tickets',
            'active' => request()->routeIs('lawyer.tickets.*'),
            'badge' => '8',
            'url' => Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.assigned_companies'),
            'icon' => 'companies',
            'active' => request()->routeIs('lawyer.companies.*'),
            'badge' => null,
            'url' => Route::has('lawyer.companies.index') ? route('lawyer.companies.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.ai_assistant'),
            'icon' => 'ai',
            'active' => request()->routeIs('lawyer.ai.*'),
            'badge' => null,
            'url' => Route::has('lawyer.ai.index') ? route('lawyer.ai.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.notifications'),
            'icon' => 'notifications',
            'active' => request()->routeIs('lawyer.notifications.*'),
            'badge' => '3',
            'url' => Route::has('lawyer.notifications.index') ? route('lawyer.notifications.index') : '#',
        ],
        [
            'label' => __('lawyer_dashboard.sidebar.account_settings'),
            'icon' => 'settings',
            'active' => request()->routeIs('lawyer.settings.*'),
            'badge' => null,
            'url' => Route::has('lawyer.settings.index') ? route('lawyer.settings.index') : '#',
        ],
    ];
@endphp

<aside id="lawyerSidebar"
    class="fixed top-0 {{ $sideClass }} z-50 h-screen w-72 {{ $hiddenClass }} overflow-y-auto bg-[#0f1b3d] text-white transition-transform duration-300 lg:translate-x-0">

    <div class="flex min-h-full flex-col px-4 py-6">

        {{-- Logo --}}
        <div class="mb-8 flex items-center gap-3 px-2">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-[#0f1b3d] shadow-sm">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M14 13l-7 7" />
                    <path d="M5 18l3 3" />
                    <path d="M14 4l6 6" />
                    <path d="M12 6l6 6" />
                    <path d="M8 10l6 6" />
                </svg>
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
                <a href="{{ $item['url'] }}"
                    class="group relative flex h-12 items-center gap-3 rounded-xl px-4 text-sm transition
                    {{ $item['active']
                        ? 'bg-[#344367] text-white font-semibold'
                        : 'text-white/60 hover:bg-white/10 hover:text-white' }}">

                    <span class="flex h-5 w-5 shrink-0 items-center justify-center">
                        @switch($item['icon'])

                            @case('dashboard')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 13h6V4H4v9z" />
                                    <path d="M14 20h6V4h-6v16z" />
                                    <path d="M4 20h6v-4H4v4z" />
                                </svg>
                            @break

                            @case('tickets')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                                    <path d="M13 6v12" />
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

                            @case('ai')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2v4" />
                                    <path d="M12 18v4" />
                                    <path d="M4.93 4.93l2.83 2.83" />
                                    <path d="M16.24 16.24l2.83 2.83" />
                                    <path d="M2 12h4" />
                                    <path d="M18 12h4" />
                                    <path d="M4.93 19.07l2.83-2.83" />
                                    <path d="M16.24 7.76l2.83-2.83" />
                                </svg>
                            @break

                            @case('notifications')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                    <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                </svg>
                            @break

                            @case('settings')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.8 1.8 0 0 0 .36 2l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.8 1.8 0 0 0-2-.36 1.8 1.8 0 0 0-1 1.64V21a2 2 0 1 1-4 0v-.09a1.8 1.8 0 0 0-1-1.64 1.8 1.8 0 0 0-2 .36l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.8 1.8 0 0 0 .36-2 1.8 1.8 0 0 0-1.64-1H3a2 2 0 1 1 0-4h.09a1.8 1.8 0 0 0 1.64-1 1.8 1.8 0 0 0-.36-2l-.06-.06A2 2 0 1 1 7.14 3.9l.06.06a1.8 1.8 0 0 0 2 .36A1.8 1.8 0 0 0 10 2.68V3a2 2 0 1 1 4 0v-.32a1.8 1.8 0 0 0 1 1.64 1.8 1.8 0 0 0 2-.36l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.8 1.8 0 0 0-.36 2A1.8 1.8 0 0 0 21 10a2 2 0 1 1 0 4h-.09a1.8 1.8 0 0 0-1.51 1z" />
                                </svg>
                            @break

                        @endswitch
                    </span>

                    <span class="font-medium">
                        {{ $item['label'] }}
                    </span>

                    @if ($item['badge'])
                        <span class="ms-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Lawyer Info --}}
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
