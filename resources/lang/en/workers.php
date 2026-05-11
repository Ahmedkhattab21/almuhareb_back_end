<?php

return [
    'page_title' => 'Workers Management',

    'breadcrumb_parent' => 'Dashboard',
    'breadcrumb_current' => 'Workers',

    'title' => 'Workers Overview',
    'subtitle' => 'Total workers, their information, and registration status inside the system.',
    'add_new' => 'Register New Worker',

    'stats' => [
        'total' => 'Total Workers',

        'active' => 'Active Workers',
        'pending' => 'pending accounts',
        'pending_hint' => 'High priority',
        'nationalities' => 'Registered Nationalities',
        'nationalities_hint' => 'Registered nationalities',
        'top_language' => 'Primary Language',
        'top_language_hint' => 'Most used language',
    ],

    'filters' => [
        'search_placeholder' => 'Search worker, company, nationality, ID number, or anything else...',
        'all_statuses' => 'All Statuses',
        'all_companies' => 'All Companies',
        'all_languages' => 'All Languages',
        'id_asc' => 'Sort by ID',
        'latest' => 'Latest',
        'name_asc' => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'apply' => 'Apply',
        'reset' => 'Clear All',
        'all_nationalities' => 'All Nationalities',
    ],

    'status' => [
        'active' => 'Active',
        'pending' => 'Under Review',
        'suspended' => 'Suspended',
        'unknown' => 'Unknown',
    ],

    'languages' => [
        'ar' => 'Arabic',
        'en' => 'English',
        'ur' => 'Urdu',
        'hi' => 'Hindi',
        'fil' => 'Filipino',
        'id' => 'Indonesian',
        'bn' => 'Bengali',
        '-' => '-',
    ],

    'table' => [
        'title' => 'Workers List',
        'showing' => 'Showing',
        'from' => 'from',
        'worker' => 'worker',
        'id' => 'ID',
        'name' => 'Worker Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'company' => 'Company',
        'nationality' => 'Nationality',
        'preferred_language' => 'Preferred Language',
        'iqama_number' => 'Iqama Number',
        'position' => 'Position',
        'status' => 'Status',
        'actions' => 'Actions',
        'empty' => 'No workers found',
        'not_assigned' => 'Not assigned',
        'no_email' => 'No email',
    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this worker?',
    ],

    'pagination' => [
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
        'previous' => 'Previous',
        'next' => 'Next',
        'page' => 'Page',
        'from' => 'of',
    ],

    'messages' => [
        'created' => 'Worker has been registered successfully.',
        'updated' => 'Worker has been updated successfully.',
        'deleted' => 'Worker has been deleted successfully.',
        'create_failed' => 'Something went wrong while registering the worker. Please try again.',
    ],

];
