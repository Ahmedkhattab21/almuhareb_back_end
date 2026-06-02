@php
    $companyUser = auth('company')->user();

    $isRtl = \Illuminate\Support\Str::startsWith(app()->getLocale(), 'ar');

    $unreadNotificationsCount =
        $companyUser && method_exists($companyUser, 'unreadNotifications')
            ? $companyUser->unreadNotifications()->count()
            : 0;

    $recentNotifications =
        $companyUser && method_exists($companyUser, 'notifications')
            ? $companyUser->notifications()->latest()->limit(5)->get()
            : collect();

    $notificationsDropdownPosition = $isRtl
        ? 'left-0 origin-top-left'
        : 'right-0 origin-top-right';

    $companyName = $companyUser->name
        ?? $companyUser->company_name
        ?? __('company_dashboard.topbar.company_name');

    $companyEmail = $companyUser->email ?? 'company@almuharib.com';

    $logoutRoute = Route::has('company.logout') ? route('company.logout') : '#';

    $profileRoute = Route::has('company.profile.show')
        ? route('company.profile.show')
        : '#';

    $workersRoute = Route::has('company.workers.index')
        ? route('company.workers.index')
        : url('/company/workers');
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white">

    <div class="flex h-20 w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        {{-- Search --}}
        <x-layout.global-search
            :url="Route::has('company.search') ? request()->getSchemeAndHttpHost() . '/company/search' : '#'"
            :placeholder="__('company_dashboard.topbar.search_placeholder')"
        />

        {{-- Actions --}}
        <div class="flex shrink-0 items-center gap-3">

            {{-- Mobile Sidebar Button --}}
            <button
                onclick="openCompanySidebar()"
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl lg:hidden"
            >
                ☰
            </button>

            {{-- Workers Quick Link --}}
            <a
                href="{{ $workersRoute }}"
                class="hidden h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-[#0f1b3d] transition hover:bg-slate-50 md:inline-flex"
            >
                <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>

                <span>{{ __('company_dashboard.sidebar.workers') }}</span>
            </a>

            {{-- Profile Dropdown --}}
            <div class="relative">

                <button
                    type="button"
                    onclick="toggleCompanyProfileMenu()"
                    class="flex items-center gap-3 rounded-2xl px-2 py-2 transition hover:bg-slate-50"
                >
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-sm font-bold text-[#0f1b3d]">
                        {{ mb_substr($companyName, 0, 1) }}
                    </div>

                    <div class="hidden text-start sm:block">
                        <p class="text-sm font-bold text-slate-900">
                            {{ $companyName }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ __('company_dashboard.topbar.company_role') }}
                        </p>
                    </div>
                </button>

                <div
                    id="companyProfileDropdown"
                    class="absolute start-0 top-[58px] z-50 hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70"
                >
                    <div class="border-b border-slate-100 px-3 py-3">
                        <p class="text-sm font-bold text-slate-900">
                            {{ $companyName }}
                        </p>

                        <p class="mt-1 truncate text-xs text-slate-500">
                            {{ $companyEmail }}
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

                        <span>{{ __('company_dashboard.topbar.profile') }}</span>
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

                            <span>{{ __('company_dashboard.topbar.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Language --}}
            <x-ui.language-switch />

            {{-- Notifications --}}
            <div class="relative" id="companyNotificationsDropdown">
                <button
                    type="button"
                    onclick="toggleCompanyNotificationsDropdown()"
                    class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white transition hover:bg-slate-50"
                >
                    <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                    </svg>

                    @if ($unreadNotificationsCount > 0)
                        <span class="absolute -top-2 -end-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">
                            {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                        </span>
                    @endif
                </button>

                <div
                    id="companyNotificationsMenu"
                    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
                    class="invisible absolute {{ $notificationsDropdownPosition }} top-[58px] z-50 w-[300px] max-w-[calc(100vw-2rem)] scale-95 rounded-2xl border border-slate-200 bg-white opacity-0 shadow-xl shadow-slate-200/70 transition-all duration-200 sm:w-[320px]"
                >
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ __('notifications.title') }}
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ __('notifications.unread') }}: {{ $unreadNotificationsCount }}
                            </p>
                        </div>

                        @if ($unreadNotificationsCount > 0 && Route::has('company.notifications.readAll'))
                            <form method="POST" action="{{ route('company.notifications.readAll') }}">
                                @csrf
                                <button type="submit" class="whitespace-nowrap text-xs font-semibold text-blue-600 transition hover:text-blue-800">
                                    {{ __('notifications.mark_all_read') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        @forelse($recentNotifications as $notification)
                            <a
                                href="{{ Route::has('company.notifications.open') ? route('company.notifications.open', $notification->id) : '#' }}"
                                class="flex gap-3 border-b border-slate-100 px-4 py-4 transition hover:bg-slate-50"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-blue-50 text-blue-600' }}">
                                    @if(str_contains($notification->type, 'ticket'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 0 0-6V6H3v3z" /><path d="M13 6v12" /></svg>
                                    @elseif(str_contains($notification->type, 'worker'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" /></svg>
                                    @elseif(str_contains($notification->type, 'lawyer'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 13l-7 7" /><path d="M14 4l6 6" /><path d="M8 10l6 6" /></svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-sm font-bold text-slate-900">
                                            {{ $notification->title }}
                                        </p>

                                        @if (!$notification->read_at)
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                                        @endif
                                    </div>

                                    @if ($notification->body)
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
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
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

                    <div class="border-t border-slate-100 p-3">
                        <a
                            href="{{ Route::has('company.notifications.index') ? route('company.notifications.index') : '#' }}"
                            class="flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
                        >
                            {{ __('company_dashboard.common.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</header>

<script>
    function toggleCompanyProfileMenu() {
        const dropdown = document.getElementById('companyProfileDropdown');

        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }

        closeCompanyNotificationsDropdown();
    }

    function toggleCompanyNotificationsDropdown() {
        const menu = document.getElementById('companyNotificationsMenu');

        if (!menu) return;

        const isHidden = menu.classList.contains('invisible');

        if (isHidden) {
            menu.classList.remove('invisible', 'opacity-0', 'scale-95');
            menu.classList.add('visible', 'opacity-100', 'scale-100');
        } else {
            closeCompanyNotificationsDropdown();
        }

        const profileDropdown = document.getElementById('companyProfileDropdown');

        if (profileDropdown) {
            profileDropdown.classList.add('hidden');
        }
    }

    function closeCompanyNotificationsDropdown() {
        const menu = document.getElementById('companyNotificationsMenu');

        if (!menu) return;

        menu.classList.add('invisible', 'opacity-0', 'scale-95');
        menu.classList.remove('visible', 'opacity-100', 'scale-100');
    }

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('companyProfileDropdown');
        const profileButton = event.target.closest('button[onclick="toggleCompanyProfileMenu()"]');

        if (!dropdown) return;

        if (!profileButton && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }

        const notificationsDropdown = document.getElementById('companyNotificationsDropdown');

        if (notificationsDropdown && !notificationsDropdown.contains(event.target)) {
            closeCompanyNotificationsDropdown();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdown = document.getElementById('companyProfileDropdown');

            if (dropdown) {
                dropdown.classList.add('hidden');
            }

            closeCompanyNotificationsDropdown();
        }
    });
</script>
