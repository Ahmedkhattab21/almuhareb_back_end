<?php

return [
    'page_title' => 'Company Positions',

    'breadcrumb_parent' => 'Company Portal',
    'breadcrumb_current' => 'Worker Positions',

    'title' => 'Worker Positions',
    'subtitle' => 'Manage job positions and titles for company workers inside the system.',
    'add_new' => 'Add New Position',

    'stats' => [
        'total' => 'Total Positions',
        'active' => 'Active Positions',
        'inactive' => 'Inactive Positions',
    ],

    'filters' => [
        'search_placeholder' => 'Search by position name...',
        'all_statuses' => 'All Statuses',
        'id_asc' => 'Oldest by ID',
        'latest' => 'Latest',
        'oldest' => 'Oldest',
        'name_asc' => 'Name A to Z',
        'name_desc' => 'Name Z to A',
        'apply' => 'Apply',
        'reset' => 'Reset',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'unknown' => 'Unknown',
    ],

    'table' => [
        'title' => 'Positions List',
        'showing' => 'Showing',
        'from' => 'from',
        'position' => 'position',
        'id' => 'ID',
        'name' => 'Position Name',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'actions' => 'Actions',
        'empty' => 'No positions found yet.',
    ],

    'actions' => [
        'show' => 'Show',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this position?',
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
        'created' => 'Position has been created successfully.',
        'updated' => 'Position data has been updated successfully.',
        'deleted' => 'Position has been deleted successfully.',
        'page_not_ready' => 'This page is not ready yet.',
    ],

    'validation' => [
        'name_required' => 'Position name is required.',
        'name_invalid' => 'Position name is invalid.',
        'name_max' => 'Position name must not exceed 255 characters.',
        'name_unique' => 'Position name is already used.',

        'status_required' => 'Position status is required.',
        'status_invalid' => 'Invalid position status.',
    ],

    'create' => [
    'page_title' => 'Add New Position',
    'breadcrumb_current' => 'Add Position',
    'title' => 'Add New Position',
    'subtitle' => 'Add a new job title to use in company worker records.',
    'back' => 'Back to Positions',
    'cancel' => 'Cancel',
    'save' => 'Save Position',
    'save_and_show' => 'Save & Show',
    'sections' => [
        'basic' => 'Basic Information',
    ],
],

'form' => [
    'name' => 'Position Name',
    'name_placeholder' => 'Example: Warehouse Worker',
    'status' => 'Status',
],

'loading' => [
    'saving' => 'Saving...',
],

'show' => [
    'page_title' => 'Position Details',
    'breadcrumb_current' => 'Position Details',
    'title' => 'Position Details',
    'subtitle' => 'View position information, status, and related system details.',
    'back' => 'Back to Positions',
    'workers_count' => 'Workers Count',
    'position_id' => 'Position ID',
    'sections' => [
        'basic' => 'Basic Information',
        'system' => 'System Information',
    ],
],


'edit' => [
    'page_title' => 'Edit Position',
    'breadcrumb_current' => 'Edit Position',
    'title' => 'Edit Position',
    'subtitle' => 'Update position information and status inside the system.',
    'show_position' => 'Show Position',
    'back' => 'Back to Positions',
    'cancel' => 'Cancel',
    'save' => 'Save Changes',
    'save_and_show' => 'Save & Show',
    'sections' => [
        'basic' => 'Basic Information',
    ],
],
];
