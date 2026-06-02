@php
    $lawyerUser = auth('lawyer')->user();

    $lawyerName = $lawyerUser->name ?? __('lawyer_dashboard.topbar.lawyer_name');
    $lawyerEmail = $lawyerUser->email ?? 'lawyer@almuharib.com';

    $logoutRoute = Route::has('lawyer.logout') ? route('lawyer.logout') : '#';
    $profileRoute = Route::has('lawyer.profile.show') ? route('lawyer.profile.show') : '#';

    $isRtl = app()->getLocale() === 'ar';

    $lawyerUnreadNotificationsCount = 0;
    $lawyerRecentNotifications = collect();

    if ($lawyerUser && class_exists(\App\Models\Notifications::class)) {
        $lawyerUnreadNotificationsCount = \App\Models\Notifications::query()
            ->where('recipient_type', get_class($lawyerUser))
            ->where('recipient_id', $lawyerUser->id)
            ->whereNull('read_at')
            ->count();

        $lawyerRecentNotifications = \App\Models\Notifications::query()
            ->where('recipient_type', get_class($lawyerUser))
            ->where('recipient_id', $lawyerUser->id)
            ->latest()
            ->limit(5)
            ->get();
    }

    $notificationsDropdownPosition = $isRtl
        ? 'left-0 origin-top-left'
        : 'right-0 origin-top-right';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white">

    <div class="flex h-20 w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        {{-- Search --}}
        <x-layout.global-search
            :url="Route::has('lawyer.search') ? request()->getSchemeAndHttpHost() . '/lawyer/search' : '#'"
            :placeholder="__('lawyer_dashboard.topbar.search_placeholder')"
        />

        {{-- Actions --}}
        <div class="flex shrink-0 items-center gap-3">

            {{-- Mobile Sidebar Button --}}
            <button
                onclick="openLawyerSidebar()"
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl lg:hidden"
            >
                ☰
            </button>

            {{-- Profile Dropdown --}}
            <div class="relative">

                <button
                    type="button"
                    onclick="toggleLawyerProfileMenu()"
                    class="flex items-center gap-3 rounded-2xl px-2 py-2 transition hover:bg-slate-50"
                >
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-sm font-bold text-[#0f1b3d]">
                        {{ mb_substr($lawyerName, 0, 1) }}
                    </div>

                    <div class="hidden text-start sm:block">
                        <p class="text-sm font-bold text-slate-900">
                            {{ $lawyerName }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ __('lawyer_dashboard.topbar.lawyer_role') }}
                        </p>
                    </div>
                </button>

                {{-- Dropdown --}}
                <div
                    id="lawyerProfileDropdown"
                    class="absolute start-0 top-[58px] z-50 hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70"
                >
                    <div class="border-b border-slate-100 px-3 py-3">
                        <p class="text-sm font-bold text-slate-900">
                            {{ $lawyerName }}
                        </p>

                        <p class="mt-1 truncate text-xs text-slate-500">
                            {{ $lawyerEmail }}
                        </p>
                    </div>

                    <a
                        href="{{ $profileRoute }}"
                        class="mt-2 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>

                        <span>{{ __('lawyer_dashboard.topbar.profile') }}</span>
                    </a>

                    <form method="POST" action="{{ $logoutRoute }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                        >
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <path d="M16 17l5-5-5-5"/>
                                <path d="M21 12H9"/>
                            </svg>

                            <span>{{ __('lawyer_dashboard.topbar.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Language --}}
            <x-ui.language-switch />

            {{-- Notifications Dropdown --}}
            <div class="relative" id="lawyerNotificationsDropdown">

                <button
                    type="button"
                    onclick="toggleLawyerNotificationsDropdown()"
                    class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white transition hover:bg-slate-50"
                >
                    <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                    </svg>

                    @if($lawyerUnreadNotificationsCount > 0)
                        <span class="absolute -top-2 -end-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">
                            {{ $lawyerUnreadNotificationsCount > 99 ? '99+' : $lawyerUnreadNotificationsCount }}
                        </span>
                    @endif
                </button>

                <div
                    id="lawyerNotificationsMenu"
                    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
                    class="invisible absolute {{ $notificationsDropdownPosition }} top-[58px] z-50 w-[300px] max-w-[calc(100vw-2rem)] scale-95 rounded-2xl border border-slate-200 bg-white opacity-0 shadow-xl shadow-slate-200/70 transition-all duration-200 sm:w-[320px]"
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ __('notifications.title') }}
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $lawyerUnreadNotificationsCount }} {{ __('notifications.unread') }}
                            </p>
                        </div>

                        @if($lawyerUnreadNotificationsCount > 0 && Route::has('lawyer.notifications.readAll'))
                            <form method="POST" action="{{ route('lawyer.notifications.readAll') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="whitespace-nowrap text-xs font-semibold text-blue-600 transition hover:text-blue-800"
                                >
                                    {{ __('notifications.mark_all_read') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- List --}}
                    <div class="max-h-80 overflow-y-auto">
                        @forelse($lawyerRecentNotifications as $notification)
                            <a
                                href="{{ Route::has('lawyer.notifications.open') ? route('lawyer.notifications.open', $notification->id) : '#' }}"
                                class="flex gap-3 border-b border-slate-100 px-4 py-4 transition hover:bg-slate-50"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl
                                    {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-blue-50 text-blue-600' }}"
                                >
                                    @if(str_contains($notification->type, 'ticket'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" />
                                            <path d="M13 6v12" />
                                        </svg>
                                    @elseif(str_contains($notification->type, 'worker'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="8" r="4" />
                                            <path d="M4 21a8 8 0 0 1 16 0" />
                                        </svg>
                                    @elseif(str_contains($notification->type, 'company'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M4 21h16" />
                                            <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-sm font-bold text-slate-900">
                                            {{ $notification->title }}
                                        </p>

                                        @if(!$notification->read_at)
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                                        @endif
                                    </div>

                                    @if($notification->body)
                                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                            {{ $notification->body }}
                                        </p>
                                    @endif

                                    <p class="mt-2 text-[11px] text-slate-400">
                                        {{ $notification->created_at?->diffForHumans() }}
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                        <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                    </svg>
                                </div>

                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ __('notifications.empty_title') }}
                                </h4>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ __('notifications.empty_body') }}
                                </p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-slate-100 p-3">
                        <a
                            href="{{ Route::has('lawyer.notifications.index') ? route('lawyer.notifications.index') : '#' }}"
                            class="flex items-center justify-center rounded-xl bg-[#0f1b3d] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#17264f]"
                        >
                            {{ __('notifications.list_title') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</header>

<script>
    function toggleLawyerProfileMenu() {
        const dropdown = document.getElementById('lawyerProfileDropdown');

        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }

        closeLawyerNotificationsDropdown();
    }

    function toggleLawyerNotificationsDropdown() {
        const menu = document.getElementById('lawyerNotificationsMenu');

        if (!menu) return;

        const isHidden = menu.classList.contains('invisible');

        if (isHidden) {
            menu.classList.remove('invisible', 'opacity-0', 'scale-95');
            menu.classList.add('visible', 'opacity-100', 'scale-100');
        } else {
            closeLawyerNotificationsDropdown();
        }

        const profileDropdown = document.getElementById('lawyerProfileDropdown');

        if (profileDropdown) {
            profileDropdown.classList.add('hidden');
        }
    }

    function closeLawyerNotificationsDropdown() {
        const menu = document.getElementById('lawyerNotificationsMenu');

        if (!menu) return;

        menu.classList.add('invisible', 'opacity-0', 'scale-95');
        menu.classList.remove('visible', 'opacity-100', 'scale-100');
    }

    document.addEventListener('click', function (event) {
        const profileDropdown = document.getElementById('lawyerProfileDropdown');
        const profileButton = event.target.closest('button[onclick="toggleLawyerProfileMenu()"]');

        if (profileDropdown && !profileButton && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.add('hidden');
        }

        const notificationsDropdown = document.getElementById('lawyerNotificationsDropdown');

        if (notificationsDropdown && !notificationsDropdown.contains(event.target)) {
            closeLawyerNotificationsDropdown();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const profileDropdown = document.getElementById('lawyerProfileDropdown');

            if (profileDropdown) {
                profileDropdown.classList.add('hidden');
            }

            closeLawyerNotificationsDropdown();
        }
    });
</script>
