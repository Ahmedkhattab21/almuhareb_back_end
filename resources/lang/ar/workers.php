<?php

return [
    'page_title' => 'إدارة العمال',

    'breadcrumb_parent' => 'لوحة التحكم',
    'breadcrumb_current' => 'العمال',

    'title' => 'نظرة عامة على العمال',
    'subtitle' => 'إجمالي عدد العمال وبياناتهم وحالة التسجيل داخل النظام.',
    'add_new' => 'تسجيل عامل جديد',

    'stats' => [
        'total' => 'إجمالي العمال',
        'active' => 'العمال النشطون',
        'pending' => 'حسابات معلقة',
        'pending_hint' => 'أولوية عالية',
        'nationalities' => 'الجنسيات المسجلة',
        'nationalities_hint' => 'جنسية مسجلة',
        'top_language' => 'اللغة الأساسية',
        'top_language_hint' => 'الأكثر استخدامًا',
        'active_hint' => 'العمال النشطون داخل النظام',
    ],

    'filters' => [
        'search_placeholder' => 'ابحث عن عامل، شركة، جنسية، رقم هوية، أو أي شيء آخر...',
        'all_statuses' => 'كل الحالات',
        'all_companies' => 'كل الشركات',
        'all_languages' => 'كل اللغات',
        'all_nationalities' => 'كل الجنسيات',
        'all_positions' => 'كل المسميات الوظيفية',
        'id_asc' => 'ترتيب حسب الرقم',
        'latest' => 'الأحدث',
        'name_asc' => 'الاسم تصاعدي',
        'name_desc' => 'الاسم تنازلي',
        'apply' => 'تطبيق',
        'reset' => 'مسح الكل',
    ],

    'status' => [
        'active' => 'نشط',
        'pending' => 'قيد الفحص',
        'suspended' => 'معلق',
        'unknown' => 'غير معروف',
    ],

    'languages' => [
        'ar' => 'العربية',
        'en' => 'الإنجليزية',
        'ur' => 'الأردية',
        'hi' => 'الهندية',
        'fil' => 'الفلبينية',
        'id' => 'الإندونيسية',
        'bn' => 'البنغالية',
        '-' => '-',
    ],

    'table' => [
        'title' => 'قائمة العمال',
        'showing' => 'عرض',
        'from' => 'من أصل',
        'worker' => 'عامل',
        'id' => 'ID',
        'name' => 'اسم العامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'company' => 'الشركة',
        'nationality' => 'الجنسية',
        'preferred_language' => 'اللغة المفضلة',
        'prefered_language' => 'اللغة المفضلة',
        'iqama_number' => 'رقم الإقامة',
        'position' => 'الوظيفة',
        'total_tickets' => 'إجمالي الاستشارات',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'empty' => 'لا يوجد عمال حاليًا',
        'not_assigned' => 'غير محدد',
        'no_email' => 'لا يوجد بريد إلكتروني',
    ],

    'actions' => [
        'import_excel' => 'استيراد من Excel',
        'show' => 'عرض',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا العامل؟',
    ],

    'pagination' => [
        'showing' => 'عرض',
        'to' => 'إلى',
        'of' => 'من',
        'results' => 'نتيجة',
        'previous' => 'السابق',
        'next' => 'التالي',
        'page' => 'صفحة',
        'from' => 'من',
    ],

    'messages' => [
        'created' => 'تم تسجيل العامل بنجاح.',
        'updated' => 'تم تحديث بيانات العامل بنجاح.',
        'deleted' => 'تم حذف العامل بنجاح.',
        'create_failed' => 'حدثت مشكلة أثناء تسجيل العامل. حاول مرة أخرى.',
    ],

    'create' => [
        'page_title' => 'إضافة عامل جديد',
        'breadcrumb_current' => 'إضافة عامل',
        'title' => 'إضافة عامل جديد',
        'subtitle' => 'إضافة عامل وربطه بالشركة والجنسية واللغة المفضلة داخل النظام.',
        'back' => 'العودة إلى العمال',

        'side_title' => 'معلومة مهمة',
        'side_text' => 'تأكد من إدخال بيانات العامل بشكل صحيح وربطه بالشركة المناسبة لضمان سهولة المتابعة.',
        'notice' => 'سيتم استخدام الجنسية واللغة المفضلة لتسهيل التواصل مع العامل وترجمة الاستشارات والرسائل داخل النظام.',

        'sections' => [
            'personal' => 'البيانات الشخصية',
            'work' => 'بيانات العمل',
            'account' => 'إعدادات الحساب',
        ],

        'cancel' => 'إلغاء',
        'save' => 'حفظ العامل',
        'save_and_add_another' => 'حفظ وإضافة عامل آخر',
    ],

    'form' => [
        'name' => 'اسم العامل',
        'name_placeholder' => 'مثال: أحمد محمد علي',

        'phone' => 'رقم الجوال',
        'email' => 'البريد الإلكتروني',

        'iqama_number' => 'رقم الهوية / الإقامة',
        'iqama_placeholder' => 'أدخل رقم الهوية أو الإقامة',

        'nationality' => 'الجنسية',
        'choose_nationality' => 'اختر الجنسية',

        'preferred_language' => 'اللغة المفضلة',
        'choose_preferred_language' => 'اختر اللغة المفضلة',

        'prefered_language' => 'اللغة المفضلة',
        'choose_prefered_language' => 'اختر اللغة المفضلة',

        'image' => 'صورة العامل',
        'image_upload' => 'ارفع صورة واضحة للعامل',
        'image_uploaded' => 'تم اختيار الصورة بنجاح',
        'image_change' => 'تغيير صورة العامل',

        'company' => 'الشركة',
        'choose_company' => 'اختر الشركة',

        'position' => 'المسمى الوظيفي',
        'choose_position' => 'اختر المسمى الوظيفي',
        'position_placeholder' => 'مثال: عامل مخزن / سائق / عامل نظافة',

        'status' => 'حالة العامل',

        'password' => 'كلمة المرور',
        'password_optional' => 'اتركها فارغة إذا كنت لا تريد تغييرها',
        'password_confirmation' => 'تأكيد كلمة المرور',
    ],

    'loading' => [
        'saving' => 'جاري الحفظ...',
    ],

    'show' => [
        'page_title' => 'عرض بيانات العامل',
        'breadcrumb_current' => 'عرض العامل',
        'title' => 'عرض بيانات العامل',
        'subtitle' => 'عرض بيانات العامل الأساسية وربطه بالشركة والجنسية واللغة المفضلة.',
        'back' => 'العودة إلى العمال',
        'worker_id' => 'رقم العامل',
        'created_at' => 'تاريخ الإضافة',
        'updated_at' => 'آخر تحديث',

        'sections' => [
            'personal' => 'البيانات الشخصية',
            'work' => 'بيانات العمل',
            'system' => 'بيانات النظام',
        ],
    ],

    'edit' => [
        'page_title' => 'تعديل بيانات العامل',
        'breadcrumb_current' => 'تعديل العامل',
        'title' => 'تعديل بيانات العامل',
        'subtitle' => 'تعديل بيانات العامل وربطه بالشركة والجنسية واللغة المفضلة.',
        'back' => 'العودة إلى العمال',
        'show_worker' => 'عرض بيانات العامل',
        'side_text' => 'يمكنك تعديل بيانات العامل، وتغيير الشركة أو المسمى الوظيفي أو اللغة المفضلة.',
        'notice' => 'في حالة عدم إدخال كلمة مرور جديدة ستبقى كلمة المرور الحالية كما هي.',
        'notes_title' => 'ملاحظات مهمة',

        'notes' => [
            'password_optional' => 'كلمة المرور اختيارية عند التعديل.',
            'image_optional' => 'اترك الصورة فارغة إذا كنت لا تريد تغييرها.',
            'company_active' => 'لا يمكن ربط العامل إلا بشركة نشطة.',
        ],

        'sections' => [
            'personal' => 'البيانات الشخصية',
            'work' => 'بيانات العمل',
            'account' => 'إعدادات الحساب',
        ],

        'cancel' => 'إلغاء',
        'save' => 'حفظ التعديلات',
    ],

    'import' => [
        'page_title' => 'استيراد العمال من Excel',
        'admin_breadcrumb' => 'الإدارة / العمال',
        'company_breadcrumb' => 'لوحة الشركة / إدارة العمال',
        'title' => 'استيراد العمال دفعة واحدة',
        'subtitle' => 'ارفع ملف CSV أو XLSX، وحدد الوظيفة والجنسية واللغة والمدينة مرة واحدة ليتم تطبيقها على كل العمال داخل الملف.',
        'download_template' => 'تحميل نموذج Excel',
        'back' => 'رجوع',
        'result_title' => 'نتيجة الاستيراد',
        'created' => 'تمت الإضافة',
        'skipped' => 'تم التخطي',
        'total_errors' => 'إجمالي الأخطاء',
        'company' => 'الشركة',
        'choose_company' => 'اختر الشركة',
        'position_id' => 'رقم الوظيفة ID',
        'no_position' => 'بدون وظيفة',
        'nationality_id' => 'رقم الجنسية ID',
        'choose_nationality' => 'اختر الجنسية',
        'language_id' => 'رقم اللغة ID',
        'choose_language' => 'اختر اللغة',
        'city_id' => 'رقم المدينة ID',
        'choose_city' => 'اختر المدينة',
        'workers_file' => 'ملف العمال',
        'start_import' => 'بدء الاستيراد',
        'sheet_columns' => 'أعمدة الشيت',
        'column_name' => 'اسم العامل',
        'column_email' => 'البريد الإلكتروني',
        'column_phone' => 'رقم الجوال',
        'column_iqama' => 'رقم الإقامة',
        'outside_sheet' => 'خارج الشيت',
        'outside_sheet_text' => 'الوظيفة والجنسية واللغة والمدينة يتم اختيارهم من الصفحة مرة واحدة لكل العمال.',
        'status_hint' => 'حالة كل عامل يتم حفظها تلقائيًا: active',
    ],
];
