@props([
    'isRtl' => true,
])

@php
    $sideClass = $isRtl ? 'right-0' : 'left-0';
    $hiddenClass = $isRtl ? 'translate-x-full' : '-translate-x-full';

    $admin = auth('admin')->user();

    $unreadNotificationsCount = ($admin && method_exists($admin, 'unreadNotifications'))
        ? $admin->unreadNotifications()->count()
        : 0;

    $newContactTicketsCount = \Illuminate\Support\Facades\Schema::hasTable('contact_tickets')
        ? \App\Models\ContactTicket::query()
            ->where('status', \App\Models\ContactTicket::STATUS_NEW)
            ->count()
        : 0;

    $routeAppPage = request()->route('app_page') ?? request()->route('appPage');
    $currentAppPageType = $routeAppPage instanceof \App\Models\AppPage ? $routeAppPage->type : null;

    $items = [
        [
            'label' => __('dashboard.sidebar.dashboard'),
            'icon' => 'home',
            'active' => request()->routeIs('admin.dashboard'),
            'badge' => null,
            'url' => route('admin.dashboard'),
        ],
        [
            'label' => __('dashboard.sidebar.companies'),
            'icon' => 'building',
            'active' => request()->routeIs('admin.companies.*'),
            'badge' => null,
            'url' => route('admin.companies.index'),
        ],
        [
            'label' => __('dashboard.sidebar.company_news'),
            'icon' => 'news',
            'active' => request()->routeIs('admin.company-news.*'),
            'badge' => null,
            'url' => Route::has('admin.company-news.index') ? route('admin.company-news.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.lawyers'),
            'icon' => 'gavel',
            'active' => request()->routeIs('admin.lawyers.*'),
            'badge' => null,
            'url' => route('admin.lawyers.index'),
        ],
        [
            'label' => __('dashboard.sidebar.workers'),
            'icon' => 'worker',
            'active' => request()->routeIs('admin.workers.*'),
            'badge' => null,
            'url' => route('admin.workers.index'),
        ],
        [
            'label' => __('dashboard.sidebar.positions'),
            'icon' => 'briefcase',
            'active' => request()->routeIs('admin.positions.*'),
            'badge' => null,
            'url' => Route::has('admin.positions.index') ? route('admin.positions.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.tickets'),
            'icon' => 'ticket',
            'active' => request()->routeIs('admin.tickets.*'),
            'badge' => null,
            'url' => Route::has('admin.tickets.index') ? route('admin.tickets.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.contact_tickets'),
            'icon' => 'contact',
            'active' => request()->routeIs('admin.contact-tickets.*'),
            'badge' => $newContactTicketsCount > 0 ? $newContactTicketsCount : null,
            'url' => Route::has('admin.contact-tickets.index') ? route('admin.contact-tickets.index') : '#',
        ],
        [
            'label' => __('dashboard.sidebar.notifications'),
            'icon' => 'bell',
            'active' => request()->routeIs('admin.notifications.*'),
            'badge' => $unreadNotificationsCount > 0 ? $unreadNotificationsCount : null,
            'url' => Route::has('admin.notifications.index') ? route('admin.notifications.index') : '#',
        ],
        [
            'label' => __('app_pages.types.privacy_policy'),
            'icon' => 'pages',
            'active' => request()->routeIs('admin.app-pages.privacy-policy')
                || $currentAppPageType === \App\Models\AppPage::TYPE_PRIVACY_POLICY,
            'badge' => null,
            'url' => Route::has('admin.app-pages.privacy-policy') ? route('admin.app-pages.privacy-policy') : '#',
        ],
        [
            'label' => __('app_pages.types.about_app'),
            'icon' => 'pages',
            'active' => request()->routeIs('admin.app-pages.about-app')
                || $currentAppPageType === \App\Models\AppPage::TYPE_ABOUT_APP,
            'badge' => null,
            'url' => Route::has('admin.app-pages.about-app') ? route('admin.app-pages.about-app') : '#',
        ],
    ];
@endphp

<aside id="adminSidebar"
    class="fixed top-0 {{ $sideClass }} z-50 h-screen w-72 {{ $hiddenClass }} overflow-y-auto bg-[#0f1b3d] text-white transition-transform duration-300 lg:translate-x-0">
    <div class="flex min-h-full flex-col px-4 py-6">

        {{-- Logo --}}
        <div class="mb-8 flex items-center gap-3 px-2">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-[#0f1b3d] shadow-sm">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M14 4l6 6" />
                    <path d="M10 8l6 6" />
                    <path d="M6 12l6 6" />
                    <path d="M4 20h12" />
                </svg>
            </div>

            <div>
                <h1 class="text-xl font-bold leading-6">
                    {{ __('dashboard.brand') }}
                </h1>
                <p class="mt-1 text-xs text-white/45">
                    {{ __('dashboard.brand_subtitle') }}
                </p>
            </div>

            <button onclick="closeSidebar()" class="ms-auto text-white/60 lg:hidden">
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

                    {{-- Icon --}}
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center">
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
                                    <path d="M9 16h1" />
                                    <path d="M14 16h1" />
                                </svg>
                            @break

                            @case('news')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 5h16v14H4z" />
                                    <path d="M8 9h8" />
                                    <path d="M8 13h5" />
                                    <path d="M16 13h.01" />
                                </svg>
                            @break

                            @case('gavel')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 13l-7 7" />
                                    <path d="M5 18l3 3" />
                                    <path d="M14 4l6 6" />
                                    <path d="M12 6l6 6" />
                                    <path d="M8 10l6 6" />
                                    <path d="M10 8l6 6" />
                                </svg>
                            @break

                            @case('worker')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
                                    <path d="M8 8h8" />
                                    <path d="M10 4v4" />
                                    <path d="M14 4v4" />
                                </svg>
                            @break

                            @case('briefcase')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1" />
                                    <path d="M4 7h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                                    <path d="M2 13h20" />
                                    <path d="M9 13v2h6v-2" />
                                </svg>
                            @break

                            @case('ticket')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                                    <path d="M13 6v12" />
                                </svg>
                            @break

                            @case('contact')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 5h16v14H4z" />
                                    <path d="M4 7l8 6 8-6" />
                                    <path d="M8 17h8" />
                                </svg>
                            @break

                            @case('bell')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                    <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                </svg>
                            @break

                            @case('pages')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M7 3h8l4 4v14H7z" />
                                    <path d="M15 3v5h5" />
                                    <path d="M10 13h7" />
                                    <path d="M10 17h5" />
                                </svg>
                            @break
                        @endswitch
                    </span>

                    {{-- Label --}}
                    <span class="font-medium">
                        {{ $item['label'] }}
                    </span>

                    {{-- Badge --}}
                    @if ($item['badge'])
                        <span
                            class="ms-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Support --}}
        <div class="mt-8 rounded-2xl border border-white/10 bg-white/10 p-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/15 bg-white/10">
                    <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M4 14v-2a8 8 0 0 1 16 0v2" />
                        <path d="M4 14a2 2 0 0 0 2 2h1v-6H6a2 2 0 0 0-2 2v2z" />
                        <path d="M20 14a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2v2z" />
                        <path d="M13 20h2a3 3 0 0 0 3-3" />
                    </svg>
                </div>

                <div>
                    <p class="text-sm font-bold text-white">
                        {{ __('dashboard.support_title') }}
                    </p>
                    <p class="mt-1 text-xs text-white/50">
                        {{ __('dashboard.support_subtitle') }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</aside>
