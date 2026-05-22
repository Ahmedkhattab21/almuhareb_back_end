<?php

return [
    'page_title' => 'Company Dashboard',

    /*
    |--------------------------------------------------------------------------
    | Brand / Sidebar
    |--------------------------------------------------------------------------
    */

    'brand' => 'Al Muhareb',
    'brand_subtitle' => 'Company Portal',

    'sidebar' => [
        'dashboard' => 'Dashboard',
        'workers' => 'Workers Management',
        'tickets' => 'Worker Tickets',
        'positions' => 'Worker Positions',
        'assigned_lawyer' => 'Assigned Lawyer',
        'notifications' => 'Notifications',
        'account_settings' => 'Account Settings',
        'company_panel' => 'Company Panel',
        'company_panel_subtitle' => 'Manage workers and legal tickets',

    ],

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    'breadcrumb_parent' => 'Company Portal',
    'breadcrumb_current' => 'Dashboard',

    'title' => 'Company Dashboard',
    'subtitle' => 'An overview of workers, legal tickets, and the assigned lawyer inside the system.',

    'overview_title' => 'Company Dashboard',
    'overview_subtitle' => 'Monitor company performance, workers, legal tickets, and the assigned lawyer from one place.',

    'actions' => [
        'add_worker' => 'Add Worker',
        'new_ticket' => 'New Ticket',
        'view_tickets' => 'View Tickets',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stats
    |--------------------------------------------------------------------------
    */

    'stats' => [
        'workers' => 'Total Workers',
        'workers_hint' => '12% increase this month',

        'open_tickets' => 'Open Tickets',
        'open_tickets_hint' => 'Needs follow-up',
        'total_tickets' => 'Total Tickets',

        'positions' => 'Positions',
        'positions_hint' => 'Based on company departments',

        'assigned_lawyer' => 'Assigned Lawyer',
        'lawyer_hint' => 'Available now for follow-up',

        'pending_workers' => 'Pending Workers',
        'pending_workers_hint' => 'Waiting for profile completion',

        'response_time' => 'Average Response Time',
    ],

    /*
    |--------------------------------------------------------------------------
    | Charts
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [
        'view_all' => 'View All',
        'not_assigned' => 'Not Assigned',
        'no_title' => 'No Title',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recent Tickets
    |--------------------------------------------------------------------------
    */

    'recent_tickets' => [
        'title' => 'Recent Tickets',
        'subtitle' => 'Latest legal complaints and inquiries submitted by workers.',
        'empty' => 'No tickets have been created yet.',
    ],

    'tickets_table' => [
        'ticket_no' => 'Ticket No.',
        'worker' => 'Worker',
        'title' => 'Ticket Subject',
        'status' => 'Status',
        'time' => 'Time',
    ],

    'ticket_status' => [
        'all' => 'All Tickets',
        'open' => 'Open',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'closed' => 'Closed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lawyer
    |--------------------------------------------------------------------------
    */

    'lawyer' => [
        'initial' => 'K',
        'name' => 'Khaled Mansour',
        'specialization' => 'Labor Lawyer',
    ],

    'lawyer_card' => [
        'title' => 'Assigned Lawyer',
        'rating' => 'Rating',
        'response' => 'Response Time',
        'response_value' => '30 min',
        'view_profile' => 'View Profile',
    ],

    /*
    |--------------------------------------------------------------------------
    | Workers
    |--------------------------------------------------------------------------
    */

    'workers' => [
        'title' => 'Recent Workers',
        'subtitle' => 'A short list of the latest workers registered under the company.',
        'empty' => 'No workers have been added yet.',
    ],

    'workers_table' => [
        'id' => 'ID',
        'name' => 'Name',
        'position' => 'Position',
        'nationality' => 'Nationality',
        'language' => 'Preferred Language',
        'status' => 'Status',
        'created_at' => 'Created At',
    ],

    'worker_status' => [
        'active' => 'Active',
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    'system_alerts' => 'System Alerts',

    'alerts' => [
        'ticket_title' => 'Ticket Requires Urgent Follow-up',
        'ticket_body' => 'There is a legal inquiry from a worker that has not been answered yet and requires company or lawyer review.',
        'view_ticket' => 'View Ticket',

        'workers_title' => 'Incomplete Worker Profiles',
        'workers_body' => 'Some worker profiles need nationality or preferred language details to ensure accurate translation and follow-up.',
        'complete_update' => 'Complete Update',

        'lawyer_title' => 'Assigned Lawyer Follow-up',
        'lawyer_body' => 'The assigned lawyer is currently available to review recent tickets and respond to legal inquiries.',
        'view_details' => 'View Details',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeline
    |--------------------------------------------------------------------------
    */

    'timeline_title' => 'Company Workflow Inside the System',
    'timeline_subtitle' => 'Start by managing workers, then follow up on legal tickets, communicate with the assigned lawyer, and view reports that help track cases.',

    'timeline' => [
        'workers' => 'Manage Workers',
        'tickets' => 'Track Tickets',
        'lawyer' => 'Assigned Lawyer',
        'reports' => 'Reports',
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Data
    |--------------------------------------------------------------------------
    */

    'demo_tickets' => [
        'ticket_1' => [
            'worker' => 'Ahmed Al-Farsi',
            'title' => 'Inquiry about working hours',
            'time' => '10 minutes ago',
        ],
        'ticket_2' => [
            'worker' => 'Mohammed Khan',
            'title' => 'Legal follow-up request',
            'time' => '1 hour ago',
        ],
        'ticket_3' => [
            'worker' => 'Saeed Ali',
            'title' => 'Complaint about salary',
            'time' => 'Yesterday',
        ],
    ],

    'demo_workers' => [
        'worker_1' => [
            'name' => 'Ahmed Al-Farsi',
            'position' => 'Warehouse Worker',
            'nationality' => 'Egyptian',
            'language' => 'Arabic',
        ],
        'worker_2' => [
            'name' => 'Mohammed Khan',
            'position' => 'Driver',
            'nationality' => 'Pakistani',
            'language' => 'Urdu',
        ],
        'worker_3' => [
            'name' => 'Saeed Ali',
            'position' => 'Operator',
            'nationality' => 'Indian',
            'language' => 'Hindi',
        ],
    ],

    'topbar' => [
    'company_name' => 'Al Muhareb Company',
    'company_role' => 'Company Manager',
    'search_placeholder' => 'Search for a ticket, worker, or file...',
    'profile' => 'Profile',
    'logout' => 'Logout',
],

'bottom' => [
    'more' => 'More',
],


];
