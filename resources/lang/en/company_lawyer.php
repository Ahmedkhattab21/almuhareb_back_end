<?php

return [
    'breadcrumb_parent' => 'Company Dashboard',
    'breadcrumb_current' => 'Assigned Lawyer',

    'status' => [
        'active' => 'Active',
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
        'unknown' => 'Unknown',
    ],

    'show' => [
        'page_title' => 'Assigned Lawyer',
        'title' => 'Assigned Company Lawyers',
        'subtitle' => 'View the lawyers assigned to your company and the case categories handled by each lawyer.',
        'back' => 'Back to Dashboard',
        'lawyer_role' => 'Company Assigned Lawyer',
        'rating' => 'Lawyer Rating',
        'personal_info' => 'Personal Information',
        'performance' => 'Performance & Follow-up',
        'company_info' => 'Company Information',
        'case_categories' => 'Case Categories',
        'no_categories' => 'No case categories assigned.',
        'latest_tickets' => 'Latest Tickets',
        'ticket' => 'Ticket',
        'no_tickets' => 'No tickets found yet.',
        'stats' => [
            'workers' => 'Company Workers',
            'total_tickets' => 'All Tickets',
            'assigned_lawyers' => 'Assigned Lawyers',
            'case_categories' => 'Case Categories',
            'active_cases' => 'Active Cases',
            'response' => 'Avg. Response',
        ],
    ],

    'empty' => [
        'title' => 'No Assigned Lawyers Yet',
        'subtitle' => 'No lawyers have been linked to this company yet. Once the admin links lawyers and case categories, they will appear here.',
    ],

    'table' => [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'status' => 'Status',
        'rating_from_5' => 'Rating from 5',
        'cases_count' => 'Cases Count',
        'response_time' => 'Response Time',
    ],

    'company' => [
        'name' => 'Company Name',
        'email' => 'Company Email',
        'phone' => 'Company Phone',
        'assigned_at' => 'Last Assignment Update',
    ],
];
