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
        'title' => 'Assigned Company Lawyer',
        'subtitle' => 'View the lawyer responsible for following up workers complaints and company legal requests.',
        'back' => 'Back to Dashboard',
        'lawyer_role' => 'Company Assigned Lawyer',
        'rating' => 'Lawyer Rating',
        'personal_info' => 'Personal Information',
        'performance' => 'Performance & Follow-up',
        'company_info' => 'Company Information',
        'latest_tickets' => 'Latest Tickets',
        'ticket' => 'Ticket',
        'no_tickets' => 'No tickets found yet.',
        'stats' => [
            'workers' => 'Company Workers',
            'open_tickets' => 'Open Tickets',
            'active_cases' => 'Active Cases',
            'response' => 'Avg. Response',
        ],
    ],

    'empty' => [
        'title' => 'No Assigned Lawyer Yet',
        'subtitle' => 'No lawyer has been assigned to this company yet. Once the admin assigns a lawyer, the lawyer details will appear here.',
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
