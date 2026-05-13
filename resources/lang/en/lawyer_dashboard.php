<?php

return [
    'page_title' => 'Lawyer Dashboard',

    'brand' => 'Al Muhareb',
    'brand_subtitle' => 'Lawyer Portal',

    'sidebar' => [
        'dashboard' => 'Dashboard',
        'tickets' => 'Legal Tickets',
        'assigned_companies' => 'Assigned Companies',
        'ai_assistant' => 'AI Assistant',
        'notifications' => 'Notifications',
        'account_settings' => 'Account Settings',
        'lawyer_panel' => 'Lawyer Panel',
        'lawyer_panel_subtitle' => 'Manage legal tickets and replies',
    ],

    'topbar' => [
        'lawyer_name' => 'Khaled Mansour',
        'lawyer_role' => 'Labor Lawyer',
        'search_placeholder' => 'Search for a ticket, company, or worker...',
        'profile' => 'Profile',
        'logout' => 'Logout',
    ],

    'bottom' => [
        'more' => 'More',
    ],

    'overview_title' => 'Lawyer Dashboard',
    'overview_subtitle' => 'Track legal tickets, assigned companies, suggested replies, and worker communication status.',

    'actions' => [
        'review_tickets' => 'Review Tickets',
        'ai_drafts' => 'Suggested Replies',
    ],

    'stats' => [
        'assigned_companies' => 'Assigned Companies',
        'open_tickets' => 'Open Tickets',
        'open_tickets_hint' => 'Needs legal review',
        'pending_replies' => 'Pending Replies',
        'pending_replies_hint' => 'Waiting for review',
        'closed_cases' => 'Closed Cases',
        'response_time' => 'Average Response Time',
        'response_time_value' => '25 min',
        'rating' => 'Rating',
        'rating_hint' => 'Based on company reviews',
    ],

    'tickets_over_time' => 'Tickets Over the Week',
    'last_7_days' => 'Last 7 Days',
    'ticket_status_chart' => 'Ticket Status',
    'active_tickets' => 'Active Tickets',

    'days' => [
        'sat' => 'Sat',
        'sun' => 'Sun',
        'mon' => 'Mon',
        'tue' => 'Tue',
        'wed' => 'Wed',
        'thu' => 'Thu',
        'fri' => 'Fri',
    ],

    'common' => [
        'view_all' => 'View All',
    ],

    'recent_tickets' => [
        'title' => 'Recent Legal Tickets',
        'subtitle' => 'Latest complaints and inquiries that need lawyer review or response.',
    ],

    'tickets_table' => [
        'ticket_no' => 'Ticket No.',
        'worker' => 'Worker',
        'company' => 'Company',
        'status' => 'Status',
        'time' => 'Time',
    ],

    'ticket_status' => [
        'new' => 'New',
        'in_review' => 'In Review',
        'replied' => 'Replied',
        'closed' => 'Closed',
    ],

    'system_alerts' => 'Lawyer Alerts',

    'alerts' => [
        'urgent_ticket_title' => 'Urgent Ticket Requires Reply',
        'urgent_ticket_body' => 'There is a legal request from a worker that has not been answered yet and needs urgent lawyer review.',
        'review_now' => 'Review Now',

        'ai_title' => 'Suggested Reply Ready',
        'ai_body' => 'The AI assistant has prepared a legal reply draft. You can review and edit it before sending.',
        'open_ai' => 'Open Draft',

        'translation_title' => 'Message Translation Ready',
        'translation_body' => 'The worker message has been translated into Arabic to make legal review easier.',
        'view_details' => 'View Details',
    ],

    'companies' => [
        'title' => 'Assigned Companies',
        'subtitle' => 'A list of companies assigned to the lawyer and their open legal tickets.',
    ],

    'companies_table' => [
        'company' => 'Company',
        'workers' => 'Workers',
        'open_tickets' => 'Open Tickets',
        'last_ticket' => 'Last Ticket',
        'status' => 'Status',
    ],

    'company_status' => [
        'active' => 'Active',
        'followup' => 'Needs Follow-up',
    ],

    'timeline_title' => 'Lawyer Workflow Inside the System',
    'timeline_subtitle' => 'The lawyer receives the ticket translated into Arabic, reviews the details, uses the AI suggested reply, and sends the response so the worker receives it in their language.',

    'timeline' => [
        'receive' => 'Receive Ticket',
        'review' => 'Review Details',
        'ai' => 'Suggest Reply',
        'reply' => 'Send Reply',
    ],

    'demo_tickets' => [
        'ticket_1' => [
            'worker' => 'Ahmed Al-Farsi',
            'company' => 'Gulf Contracting Company',
            'time' => '10 minutes ago',
        ],
        'ticket_2' => [
            'worker' => 'Mohammed Khan',
            'company' => 'Al Madinah Services',
            'time' => '1 hour ago',
        ],
        'ticket_3' => [
            'worker' => 'Saeed Ali',
            'company' => 'Elite Operations Company',
            'time' => 'Yesterday',
        ],
    ],

    'demo_companies' => [
        'company_1' => [
            'name' => 'Gulf Contracting Company',
            'last_ticket' => '10 minutes ago',
        ],
        'company_2' => [
            'name' => 'Al Madinah Services',
            'last_ticket' => '1 hour ago',
        ],
        'company_3' => [
            'name' => 'Elite Operations Company',
            'last_ticket' => 'Yesterday',
        ],
    ],
];
