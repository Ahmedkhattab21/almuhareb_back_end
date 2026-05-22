@extends($layout)

@section('title', __('profile.page_title'))

@section('content')
@php
    $displayName = $user->name ?? $user->company_name ?? __('profile.default_account_name');
    $displayEmail = $user->email ?? '-';
    $avatarLetter = mb_substr($displayName, 0, 1);

    $status = $user->status ?? 'active';
    $statusLabel = __('profile.status.' . $status);

    if ($statusLabel === 'profile.status.' . $status) {
        $statusLabel = $status;
    }
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-500">
                {{ __('profile.account') }}
            </p>

            <h1 class="mt-2 text-2xl font-bold text-[#0f1b3d] sm:text-3xl">
                {{ __('profile.page_title') }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ __('profile.admin_page_subtitle') }}
            </p>
        </div>

        <a
            href="{{ url()->previous() }}"
            class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-[#0f1b3d] transition hover:bg-slate-50"
        >
            <svg class="h-5 w-5 {{ app()->getLocale() === 'ar' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M19 12H5" />
                <path d="M12 19l-7-7 7-7" />
            </svg>

            {{ __('profile.back') }}
        </a>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-3xl bg-blue-50 text-5xl font-black text-[#0f1b3d]">
                    {{ $avatarLetter }}
                </div>

                <div>
                    <div class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">
                        {{ $statusLabel }}
                    </div>

                    <h2 class="mt-3 text-2xl font-black text-[#0f1b3d]">
                        {{ $displayName }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $roleLabel }}
                    </p>
                </div>
            </div>

            <div class="grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 px-5 py-4">
                    <p class="text-xs font-bold text-slate-400">
                        {{ __('profile.email') }}
                    </p>

                    <p class="mt-1 font-bold text-[#0f1b3d]">
                        {{ $displayEmail }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 px-5 py-4">
                    <p class="text-xs font-bold text-slate-400">
                        {{ __('profile.account_number') }}
                    </p>

                    <p class="mt-1 font-bold text-[#0f1b3d]">
                        #{{ $user->id }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ $updateRoute }}" data-loading-form class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-200 p-6">
            <h2 class="text-xl font-black text-[#0f1b3d]">
                {{ __('profile.edit_data') }}
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                {{ __('profile.edit_subtitle') }}
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2">
            @foreach($fields as $field => $label)
                @php
                    $value = old($field, $user->{$field} ?? '');
                    $isLong = in_array($field, ['address'], true);
                    $isStatus = $field === 'status';
                    $isLanguage = $field === 'preferred_language';

                    $translatedFieldLabel = __('profile.fields.' . $field);

                    if ($translatedFieldLabel === 'profile.fields.' . $field) {
                        $translatedFieldLabel = $label;
                    }
                @endphp

                <div class="{{ $isLong ? 'md:col-span-2' : '' }}">
                    <label for="{{ $field }}" class="mb-2 block text-sm font-bold text-slate-600">
                        {{ $translatedFieldLabel }}
                    </label>

                    @if($isLong)
                        <textarea
                            id="{{ $field }}"
                            name="{{ $field }}"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                        >{{ $value }}</textarea>
                    @elseif($isStatus)
                        <select
                            id="{{ $field }}"
                            name="{{ $field }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                        >
                            <option value="active" @selected($value === 'active')>
                                {{ __('profile.status.active') }}
                            </option>

                            <option value="inactive" @selected($value === 'inactive')>
                                {{ __('profile.status.inactive') }}
                            </option>

                            <option value="suspended" @selected($value === 'suspended')>
                                {{ __('profile.status.suspended') }}
                            </option>
                        </select>
                    @elseif($isLanguage)
                        <select
                            id="{{ $field }}"
                            name="{{ $field }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                        >
                            <option value="ar" @selected($value === 'ar')>
                                {{ __('profile.languages.ar') }}
                            </option>

                            <option value="en" @selected($value === 'en')>
                                {{ __('profile.languages.en') }}
                            </option>
                        </select>
                    @else
                        <input
                            id="{{ $field }}"
                            type="{{ $field === 'email' ? 'email' : 'text' }}"
                            name="{{ $field }}"
                            value="{{ $value }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                        >
                    @endif

                    @error($field)
                        <p class="mt-2 text-xs font-bold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            @endforeach

            <div>
                <label for="password" class="mb-2 block text-sm font-bold text-slate-600">
                    {{ __('profile.new_password') }}
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="{{ __('profile.password_placeholder') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                >

                @error('password')
                    <p class="mt-2 text-xs font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-600">
                    {{ __('profile.password_confirmation') }}
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#0f1b3d] outline-none transition focus:border-blue-400 focus:bg-white"
                >
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-200 p-6">
            <button
                type="submit"
                data-loading-button
                data-loading-text="{{ __('profile.saving') }}"
                class="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-[#0f1b3d] px-6 text-sm font-bold text-white shadow-lg shadow-slate-300 transition hover:bg-[#152554]"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5" />
                </svg>

                {{ __('profile.save_changes') }}
            </button>
        </div>
    </form>
</div>
@endsection
