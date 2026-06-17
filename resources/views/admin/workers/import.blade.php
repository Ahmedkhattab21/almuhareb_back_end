@extends('layouts.app')

@section('title', 'استيراد العمال من Excel')

@section('content')
<div class="space-y-8" dir="rtl">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-bold text-slate-500">الإدارة / العمال</p>
            <h1 class="mt-2 text-4xl font-black text-[#0f1b3d]">استيراد العمال دفعة واحدة</h1>
            <p class="mt-2 text-sm leading-7 text-slate-500">
                ارفع ملف CSV أو XLSX، وحدد بيانات الوظيفة والجنسية واللغة مرة واحدة ليتم تطبيقها على كل العمال داخل الملف.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.workers.import.template') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-extrabold text-[#0f1b3d]">
                تحميل نموذج Excel
            </a>
            <a href="{{ route('admin.workers.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#0f1b3d] px-5 text-sm font-extrabold text-white">
                رجوع
            </a>
        </div>
    </div>

    @if(session('import_result'))
        @php($result = session('import_result'))
        <div class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">نتيجة الاستيراد</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-green-50 p-4 text-green-700">
                    <p class="text-sm font-bold">تمت الإضافة</p>
                    <p class="mt-2 text-3xl font-black">{{ $result['created'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl bg-amber-50 p-4 text-amber-700">
                    <p class="text-sm font-bold">تم التخطي</p>
                    <p class="mt-2 text-3xl font-black">{{ $result['skipped'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                    <p class="text-sm font-bold">إجمالي الأخطاء</p>
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
        <form method="POST" action="{{ route('admin.workers.import.store') }}" enctype="multipart/form-data" class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">الشركة *</label>
                    <select name="company_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">اختر الشركة</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                                #{{ $company->id }} - {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">الوظيفة ID</label>
                    <select name="position_id" class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">بدون وظيفة</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                #{{ $position->id }} - {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">الجنسية ID *</label>
                    <select name="nationality_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">اختر الجنسية</option>
                        @foreach($nationalities as $nationality)
                            <option value="{{ $nationality->id }}" @selected(old('nationality_id') == $nationality->id)>
                                #{{ $nationality->id }} - {{ $nationality->nationality }}
                            </option>
                        @endforeach
                    </select>
                    @error('nationality_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">المدينة ID *</label>
                    <select name="city_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">اختر المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                #{{ $city->id }} - {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">اللغة ID *</label>
                    <select name="preferred_language_id" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold outline-none">
                        <option value="">اختر اللغة</option>
                        @foreach($preferedLanguages as $language)
                            <option value="{{ $language->id }}" @selected(old('preferred_language_id') == $language->id)>
                                #{{ $language->id }} - {{ $language->prefered_language }} ({{ $language->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('preferred_language_id')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-extrabold text-[#0f1b3d]">ملف العمال *</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx" required class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                    @error('file')<p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <button class="mt-6 h-12 rounded-2xl bg-[#0f1b3d] px-8 text-sm font-extrabold text-white">
                بدء الاستيراد
            </button>
        </form>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-[#0f1b3d]">أعمدة الشيت</h2>
            <div class="mt-4 space-y-3 text-sm font-bold leading-7 text-slate-600">
                <p><span class="text-[#0f1b3d]">name</span> اسم العامل *</p>
                <p><span class="text-[#0f1b3d]">email</span> البريد الإلكتروني</p>
                <p><span class="text-[#0f1b3d]">phone</span> رقم الجوال *</p>
                <p><span class="text-[#0f1b3d]">iqama_number</span> رقم الإقامة</p>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm font-bold leading-7 text-slate-600">
                <p class="text-[#0f1b3d]">خارج الشيت</p>
                <p>الوظيفة والجنسية واللغة والمدينة يتم اختيارهم من الصفحة مرة واحدة لكل العمال.</p>
                <p>حالة كل عامل يتم حفظها تلقائيًا: active</p>
            </div>
        </div>
    </div>
</div>
@endsection
