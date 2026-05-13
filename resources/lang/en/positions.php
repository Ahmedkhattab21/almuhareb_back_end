<?php

return [
    'page_title' => 'Manage Positions',
    'breadcrumb_parent' => 'Dashboard',
    'breadcrumb_current' => 'Positions',
    'title' => 'Manage Positions',
    'subtitle' => 'Manage available job positions and link them to workers.',
    'sidebar' => 'Positions',
    'add_new' => 'Add New Position',

    'stats' => [
        'total' => 'Total Positions',
        'active' => 'Active Positions',
        'inactive' => 'Inactive Positions',

    ],

    'filters' => [
        'search_placeholder' => 'Search by position name',
        'all_statuses' => 'All Statuses',
        'id_asc' => 'Oldest by ID',
        'latest' => 'Latest',
        'name_asc' => 'Name Ascending',
        'name_desc' => 'Name Descending',
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
        'position' => 'Position',
        'id' => 'ID',
        'name' => 'Position Name',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Last Updated',
        'actions' => 'Actions',
        'empty' => 'No positions found.',

    ],

    'form' => [
        'name' => 'Position Name',
        'name_placeholder' => 'Example: Warehouse Worker / Driver / Cleaner',
        'status' => 'Position Status',

    ],

    'actions' => [
        'show' => 'Show',

        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this position?',
    ],

    'create' => [
        'page_title' => 'Add New Position',
        'breadcrumb_current' => 'Add Position',
        'title' => 'Add New Position',
        'subtitle' => 'Add a new job position to use when creating workers.',
        'back' => 'Back to Positions',
        'cancel' => 'Cancel',
        'save' => 'Save Position',
        'save_and_show' => 'Save and Show',

        'sections' => [
            'basic' => 'Position Information',
        ],

    ],

    'show' => [
        'page_title' => 'Position Details',
        'breadcrumb_current' => 'Position Details',
        'title' => 'Position Details',
        'subtitle' => 'View position details and linked workers count.',
        'back' => 'Back to Positions',
        'position_id' => 'Position ID',
        'workers_count' => 'Workers Count',

        'sections' => [
            'basic' => 'Basic Information',
            'system' => 'System Information',
        ],

    ],

    'messages' => [
        'created' => 'Position has been created successfully.',
        'create_failed' => 'An error occurred while creating the position.',
        'updated' => 'Position has been updated successfully.',
        'update_failed' => 'An error occurred while updating the position.',
        'deleted' => 'Position has been deleted successfully.',
        'delete_failed' => 'An error occurred while deleting the position.',
        'delete_has_workers' => 'This position cannot be deleted because it is linked to workers.',

    ],

    'loading' => [
        'saving' => 'Saving...',
    ],

    'edit' => [
        'page_title' => 'Edit Position',
        'breadcrumb_current' => 'Edit Position',
        'title' => 'Edit Position Details',
        'subtitle' => 'Edit the position name and activation status.',
        'back' => 'Back to Positions',
        'show_position' => 'View Position',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
        'save_and_show' => 'Save and Show',

        'sections' => [
            'basic' => 'Position Information',
        ],

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
];
