@props([
    'url',
    'placeholder' => 'ابحث...',
])

@php
    $searchId = 'global-search-' . uniqid();
    $searchUrl = $url;
@endphp

<div id="{{ $searchId }}" class="relative w-full max-w-md">
    <div class="flex items-center gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <span class="text-slate-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
        </span>

        <input
            type="text"
            data-global-search-input
            data-search-url="{{ $searchUrl }}"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400"
        >
    </div>

    <div
        data-global-search-menu
        class="invisible absolute top-[54px] z-50 w-full scale-95 overflow-hidden rounded-2xl border border-slate-200 bg-white opacity-0 shadow-xl shadow-slate-200/70 transition-all duration-150"
    >
        <div data-global-search-results class="max-h-96 overflow-y-auto"></div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-global-search-input]').forEach(function (input) {
                let timer = null;
                const root = input.closest('[id^="global-search-"]');
                const menu = root?.querySelector('[data-global-search-menu]');
                const results = root?.querySelector('[data-global-search-results]');

                function closeMenu() {
                    if (!menu) return;
                    menu.classList.add('invisible', 'opacity-0', 'scale-95');
                    menu.classList.remove('visible', 'opacity-100', 'scale-100');
                }

                function openMenu() {
                    if (!menu) return;
                    menu.classList.remove('invisible', 'opacity-0', 'scale-95');
                    menu.classList.add('visible', 'opacity-100', 'scale-100');
                }

                function render(items, query) {
                    if (!results) return;

                    if (!query || query.length < 2) {
                        results.innerHTML = '';
                        closeMenu();
                        return;
                    }

                    if (!items.length) {
                        results.innerHTML = '<div class="px-5 py-8 text-center text-sm font-bold text-slate-500">لا توجد نتائج مطابقة.</div>';
                        openMenu();
                        return;
                    }

                    results.innerHTML = items.map(function (item) {
                        const title = escapeHtml(item.title || '-');
                        const type = escapeHtml(item.type || '-');
                        const subtitle = escapeHtml(item.subtitle || '-');
                        const url = escapeAttribute(normalizeResultUrl(item.url || '#'));

                        return `
                            <a href="${url}" class="flex gap-3 border-b border-slate-100 px-4 py-3 transition hover:bg-slate-50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#eef3ff] text-xs font-black text-[#5368aa]">${type.substring(0, 2)}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-black text-[#0f1b3d]">${title}</span>
                                    <span class="mt-1 block truncate text-xs font-bold text-slate-400">${type} - ${subtitle}</span>
                                </span>
                            </a>
                        `;
                    }).join('');
                    openMenu();
                }

                function escapeHtml(value) {
                    return String(value)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function escapeAttribute(value) {
                    return escapeHtml(value).replaceAll('`', '&#096;');
                }

                function normalizeResultUrl(value) {
                    if (!value || value === '#') {
                        return '#';
                    }

                    try {
                        const parsed = new URL(value, window.location.origin);

                        if (parsed.hostname === window.location.hostname) {
                            return parsed.pathname + parsed.search + parsed.hash;
                        }

                        return parsed.toString();
                    } catch (error) {
                        return value;
                    }
                }

                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    const query = input.value.trim();

                    if (query.length < 2) {
                        render([], query);
                        return;
                    }

                    timer = setTimeout(function () {
                        search(query)
                            .then(function (items) {
                                render(items, query);
                            })
                            .catch(function () {
                                if (!results) return;
                                results.innerHTML = '<div class="px-5 py-8 text-center text-sm font-bold text-red-500">تعذر تنفيذ البحث. أعد تحميل الصفحة وحاول مرة أخرى.</div>';
                                openMenu();
                            });
                    }, 250);
                });

                async function search(query) {
                    const endpoints = searchEndpoints(query);
                    let lastError = null;

                    for (const endpoint of endpoints) {
                        try {
                            const payload = await fetchSearch(endpoint);
                            const items = payload?.data?.results || [];

                            if (items.length) {
                                return items;
                            }
                        } catch (error) {
                            lastError = error;
                        }
                    }

                    if (lastError) {
                        throw lastError;
                    }

                    return [];
                }

                function searchEndpoints(query) {
                    const paths = [];

                    if (input.dataset.searchUrl && input.dataset.searchUrl !== '#') {
                        paths.push(input.dataset.searchUrl);
                    }

                    if (window.location.pathname.startsWith('/admin')) {
                        paths.push('/admin/search');
                    } else if (window.location.pathname.startsWith('/lawyer')) {
                        paths.push('/lawyer/search');
                    } else if (window.location.pathname.startsWith('/company')) {
                        paths.push('/company/search');
                    }

                    return [...new Set(paths.map(function (path) {
                        const endpoint = new URL(path, window.location.origin);
                        endpoint.searchParams.set('q', query);

                        return endpoint.toString();
                    }))];
                }

                async function fetchSearch(url) {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        redirect: 'follow'
                    });

                    const text = await response.text();

                    if (!response.ok) {
                        throw new Error('Search request failed');
                    }

                    try {
                        return JSON.parse(text.replace(/^\uFEFF/, ''));
                    } catch (error) {
                        throw new Error('Search response is not JSON');
                    }
                }

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeMenu();
                    }
                });

                document.addEventListener('click', function (event) {
                    if (root && !root.contains(event.target)) {
                        closeMenu();
                    }
                });
            });
        });
    </script>
@endonce
