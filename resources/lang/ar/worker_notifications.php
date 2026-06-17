<?php

return [
    'types' => [
        'company_news_created' => [
            'title' => 'خبر جديد من الشركة',
            'body' => ':news_title',
        ],
        'worker_created' => [
            'title' => 'تم إنشاء حسابك',
            'body' => 'تم إضافة حساب العامل :worker_name داخل النظام.',
        ],
        'worker_updated' => [
            'title' => 'تم تحديث بياناتك',
            'body' => 'تم تحديث بيانات العامل :worker_name.',
        ],
        'worker_deleted' => [
            'title' => 'تم حذف عامل',
            'body' => 'تم حذف العامل :worker_name من النظام.',
        ],
        'ticket_created' => [
            'title' => 'تم إنشاء تذكرة جديدة',
            'body' => 'تم إنشاء تذكرتك رقم :ticket_id بنجاح.',
        ],
        'ticket_message_created' => [
            'title' => 'رد جديد على التذكرة',
            'body' => 'يوجد رد جديد على التذكرة رقم :ticket_id.',
        ],
        'ticket_status_updated' => [
            'title' => 'تم تحديث حالة التذكرة',
            'body' => 'تم تحديث حالة التذكرة رقم :ticket_id.',
        ],
        'ticket_closed' => [
            'title' => 'تم إغلاق التذكرة',
            'body' => 'تم إغلاق التذكرة رقم :ticket_id.',
        ],
    ],
];
