@extends('layouts.company')

@section('title', __('workers.import.page_title'))

@section('content')
<div class="space-y-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-bold text-slate-500">{{ __('workers.import.company_breadcrumb') }}</p>
            <h1 class="mt-2 text-4xl font-black text-[#0f1b3d]">{{ __('workers.import.title') }}</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ __('workers.import.subtitle') }}</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('company.workers.import.template') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-extrabold text-[#0f1b3d]">
                {{ __('workers.import.download_template') }}
            </a>
            <a href="{{ route('company.workers.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white">
                {{ __('workers.import.back') }}
            </a>
        </div>
    </div>

    @if(session('import_result'))
        @php($result = session('import_result'))
        <div class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">{{ __('workers.import.result_title') }}</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-green-50 p-4 text-green-700">
                    <p class="text-sm font-bold">{{ __('workers.import.created') }}</p>
                    <p class="mt-2 text-3xl font-black">{{ $result['created'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl bg-amber-50 p-4 text-amber-700">
                    <p class="text-sm font-bold">{{ __('workers.import.skipped') }}</p>
                    <p class="mt-2 text-3xl font-black">{{ $result['skipped'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                    <p class="text-sm font-bold">{{ __('workers.import.total_errors') }}</p>
                    <p class="mt-2 text-3xl font-black">{{ $result['total_errors'] ?? 0 }}</p>
                </div>
            </div>

            @if(! empty($result['errors']))
                <div class="mt-5 rounded-2xl bg-red-50 p-4 text-sm font-bold leading-7 text-red-700">
                    @foreach($result['errors'] as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <form method="POST" action="{{ route('company.workers.import.store') }}" enctype="multipart/form-data" class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">{{ __('workers.import.position_id') }}</label>
                    <select name="position_id" class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">{{ __('workers.import.no_position') }}</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                #{{ $position->id }} - {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">{{ __('workers.import.nationality_id') }} *</label>
                    <select name="nationality_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">{{ __('workers.import.choose_nationality') }}</option>
                        @foreach($nationalities as $nationality)
                            <option value="{{ $nationality->id }}" @selected(old('nationality_id') == $nationality->id)>
                                #{{ $nationality->id }} - {{ $nationality->nationality }}
                            </option>
                        @endforeach
                    </select>
                    @error('nationality_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">{{ __('workers.import.language_id') }} *</label>
                    <select name="preferred_language_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">{{ __('workers.import.choose_language') }}</option>
                        @foreach($preferedLanguages as $language)
                            <option value="{{ $language->id }}" @selected(old('preferred_language_id') == $language->id)>
                                #{{ $language->id }} - {{ $language->prefered_language }} ({{ $language->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('preferred_language_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">{{ __('workers.import.city_id') }} *</label>
                    <select name="city_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">{{ __('workers.import.choose_city') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                #{{ $city->id }} - {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">{{ __('workers.import.workers_file') }} *</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                    @error('file')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <button class="mt-6 h-12 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white">
                {{ __('workers.import.start_import') }}
            </button>
        </form>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">{{ __('workers.import.sheet_columns') }}</h2>
            <div class="mt-4 space-y-3 text-sm font-bold leading-7 text-slate-600">
                <p><span class="text-[#0f1b3d]">name</span> {{ __('workers.import.column_name') }} *</p>
                <p><span class="text-[#0f1b3d]">email</span> {{ __('workers.import.column_email') }}</p>
                <p><span class="text-[#0f1b3d]">phone</span> {{ __('workers.import.column_phone') }} *</p>
                <p><span class="text-[#0f1b3d]">iqama_number</span> {{ __('workers.import.column_iqama') }}</p>
                <p><span class="text-[#0f1b3d]">operating_company</span> {{ __('workers.import.column_operating_company') }}</p>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm font-bold leading-7 text-slate-600">
                <p class="text-[#0f1b3d]">{{ __('workers.import.outside_sheet') }}</p>
                <p>{{ __('workers.import.outside_sheet_text') }}</p>
                <p>{{ __('workers.import.status_hint') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
