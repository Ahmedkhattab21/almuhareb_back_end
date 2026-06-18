<?php

return [
    'breadcrumb_parent' => 'Company Dashboard',
    'breadcrumb_current' => 'Assigned Consultant',

    'status' => [
        'active' => 'Active',
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
        'unknown' => 'Unknown',
    ],

    'show' => [
        'page_title' => 'Assigned Consultant',
        'title' => 'Assigned Company Consultants',
        'subtitle' => 'View the consultants assigned to your company and the case categories handled by each consultant.',
        'back' => 'Back to Dashboard',
        'lawyer_role' => 'Company Assigned Consultant',
        'rating' => 'Consultant Rating',
        'personal_info' => 'Personal Information',
        'performance' => 'Performance & Follow-up',
        'company_info' => 'Company Information',
        'case_categories' => 'Case Categories',
        'no_categories' => 'No case categories assigned.',
        'latest_tickets' => 'Latest Consultations',
        'ticket' => 'Consultation',
        'no_tickets' => 'No consultations found yet.',
        'stats' => [
            'workers' => 'Company Workers',
            'total_tickets' => 'All Consultations',
            'assigned_lawyers' => 'Assigned Consultants',
            'case_categories' => 'Case Categories',
            'active_cases' => 'Active Cases',
            'response' => 'Avg. Response',
        ],
    ],

    'empty' => [
        'title' => 'No Assigned Consultants Yet',
        'subtitle' => 'No consultants have been linked to this company yet. Once the admin links consultants and case categories, they will appear here.',
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
