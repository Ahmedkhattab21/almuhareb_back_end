<?php

return [
    'page_title' => 'Company Workers',

    'breadcrumb_parent' => 'Company Portal',
    'breadcrumb_current' => 'Workers',

    'title' => 'Company Workers',
    'subtitle' => 'Manage your company workers and track their information and status inside the system.',
    'add_new' => 'Add New Worker',

    'stats' => [
        'total' => 'Total Workers',
        'active' => 'Active Workers',
        'active_hint' => 'Active workers inside the system',
        'pending' => 'Pending Review',
        'pending_hint' => 'Workers need data completion',
        'suspended' => 'Suspended Workers',
        'suspended_hint' => 'Temporarily suspended workers',
    ],

    'filters' => [
        'search_placeholder' => 'Search by worker name, phone, iqama, or position...',
        'all_statuses' => 'All Statuses',
        'all_nationalities' => 'All Nationalities',
        'all_languages' => 'All Languages',
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
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
        'unknown' => 'Unknown',
    ],

    'table' => [
        'title' => 'Workers List',
        'showing' => 'Showing',
        'from' => 'from',
        'worker' => 'worker',
        'id' => 'ID',
        'name' => 'Worker Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'iqama_number' => 'Iqama Number',
        'position' => 'Position',
        'nationality' => 'Nationality',
        'language' => 'Preferred Language',
        'status' => 'Status',
        'total_tickets' => 'Total Consultations',
        'open_tickets' => 'Open Consultations',
        'actions' => 'Actions',
        'empty' => 'No workers found yet.',
    ],

    'actions' => [
        'show' => 'Show',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'import_excel' => 'Import from Excel',
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
        'created' => 'Worker has been created successfully.',
        'updated' => 'Worker data has been updated successfully.',
        'deleted' => 'Worker has been deleted successfully.',
        'page_not_ready' => 'This page is not ready yet.',
    ],

    'validation' => [
        'name_required' => 'Worker name is required.',
        'name_max' => 'Worker name must not exceed 255 characters.',

        'email_invalid' => 'Please enter a valid email address.',
        'email_max' => 'Email must not exceed 255 characters.',

        'phone_max' => 'Phone number must not exceed 30 characters.',

        'iqama_unique' => 'Iqama number is already used.',
        'iqama_max' => 'Iqama number must not exceed 50 characters.',

        'position_max' => 'Position must not exceed 255 characters.',
        'nationality_max' => 'Nationality must not exceed 100 characters.',
        'language_max' => 'Preferred language must not exceed 100 characters.',

        'status_required' => 'Worker status is required.',
        'status_invalid' => 'Invalid worker status.',

        'open_tickets_integer' => 'Open consultations count must be a number.',
        'open_tickets_min' => 'Open consultations count cannot be less than zero.',
        'password_required' => 'Password is required.',
'password_min' => 'Password must be at least 6 characters.',
'password_confirmed' => 'Password confirmation does not match.',
'position_invalid' => 'The selected position is invalid.',
'nationality_invalid' => 'The selected nationality is invalid.',
'language_invalid' => 'The selected language is invalid.',
'image_invalid' => 'The uploaded file must be an image.',
'image_mimes' => 'The image must be JPG, PNG, or WEBP.',
'image_max' => 'The image size must not exceed 2 MB.',

'email_unique' => 'This email address is already used.',

    ],
    'show' => [
        'page_title' => 'Worker Details',
        'breadcrumb_current' => 'Worker Details',
        'title' => 'Worker Details',
        'subtitle' => 'View worker information, job details, and status inside the company.',
        'back' => 'Back to Workers',
        'worker_id' => 'Worker ID',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'company' => 'Company',
        'sections' => [
            'personal' => 'Personal Details',
            'work' => 'Work Details',
            'system' => 'System Details',
        ],
    ],

    'form' => [
        'name' => 'Worker Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'iqama_number' => 'Iqama Number',
        'company' => 'Company',
        'position' => 'Position',
        'nationality' => 'Nationality',
        'language' => 'Preferred Language',
        'status' => 'Status',

        'name_placeholder' => 'Enter worker name',
        'iqama_placeholder' => 'Example: 2000000000',

        'choose_nationality' => 'Choose nationality',

        'choose_language' => 'Choose preferred language',

        'choose_position' => 'Choose position',

        'image' => 'Worker Image',
        'image_upload' => 'Click to upload worker image',
        'image_change' => 'Change worker image',
        'image_uploaded' => 'Image selected',

        'password' => 'Password',
        'password_optional' => 'Leave empty if you do not want to change it',
        'password_confirmation' => 'Password Confirmation',
    ],

    'edit' => [
        'page_title' => 'Edit Worker',
        'breadcrumb_current' => 'Edit Worker',
        'title' => 'Edit Worker Data',
        'subtitle' => 'Update the data of a worker under your company.',
        'show_worker' => 'Show Worker',
        'back' => 'Back to Workers',
        'side_text' => 'You can update the worker profile and job information from here.',
        'notes_title' => 'Important Notes',
        'notice' => 'The company can only edit workers assigned to it.',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',

        'sections' => [
            'personal' => 'Personal Data',
            'work' => 'Work Data',
            'account' => 'Account Settings',
        ],

        'notes' => [
            'password_optional' => 'Leave the password empty if you do not want to change it.',
            'image_optional' => 'Uploading a new image is optional.',
            'company_fixed' => 'The company is fixed and cannot be changed from this page.',
        ],
    ],

    'loading' => [
        'saving' => 'Saving...',
    ],

'create' => [
    'page_title' => 'Add New Worker',
    'breadcrumb_current' => 'Add Worker',
    'title' => 'Add New Worker',
    'subtitle' => 'Add a new worker under your company inside the system.',
    'back' => 'Back to Workers',
    'side_title' => 'Worker Information',
    'side_text' => 'Enter the worker personal and job information. The worker will be linked to your company automatically.',
    'notice' => 'The worker will be added under your current company account only.',
    'cancel' => 'Cancel',
    'save' => 'Save Worker',
    'save_and_add_another' => 'Save & Add Another',
    'sections' => [
        'personal' => 'Personal Data',
        'work' => 'Work Data',
        'account' => 'Account Settings',
    ],
],
];
