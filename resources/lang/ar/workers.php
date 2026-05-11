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
        'pending' =>' حسابات معلقه ',
        'pending_hint' => 'أولوية عالية',
        'nationalities' => 'الجنسيات المسجلة',
        'nationalities_hint' => 'جنسية مسجلة',
        'top_language' => 'اللغة الأساسية',
        'top_language_hint' => 'الأكثر استخدامًا',
    ],

    'filters' => [
        'search_placeholder' => 'ابحث عن عامل، شركة، جنسية، رقم هوية، أو أي شيء آخر...',
        'all_statuses' => 'كل الحالات',
        'all_companies' => 'كل الشركات',
        'all_languages' => 'كل اللغات',
        'id_asc' => 'ترتيب حسب الرقم',
        'latest' => 'الأحدث',
        'name_asc' => 'الاسم تصاعدي',
        'name_desc' => 'الاسم تنازلي',
        'apply' => 'تطبيق',
        'reset' => 'مسح الكل',
        'all_nationalities' => 'كل الجنسيات',
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
        'iqama_number' => 'رقم الإقامة',
        'position' => 'الوظيفة',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'empty' => 'لا يوجد عمال حاليًا',
        'not_assigned' => 'غير محدد',
        'no_email' => 'لا يوجد بريد إلكتروني',
    ],

    'actions' => [
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

];
