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
        'active_hint' => 'Active workers in the system',

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
        'all_positions' => 'All Positions',
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
        'total_tickets' => 'Total Tickets',
        'status' => 'Status',
        'actions' => 'Actions',
        'empty' => 'No workers found',
        'not_assigned' => 'Not assigned',
        'no_email' => 'No email',

        'prefered_language' => 'Preferred Language',
    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this worker?',
        'show' => 'Show',
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

    'create' => [
        'page_title' => 'Add New Worker',
        'breadcrumb_current' => 'Add Worker',
        'title' => 'Add New Worker',
        'subtitle' => 'Add a worker and link them to the company, nationality, and preferred language.',
        'back' => 'Back to Workers',

        'side_title' => 'Important Information',
        'side_text' => 'Make sure the worker data is correct and linked to the proper company for easier follow-up.',

        'notice' => 'Nationality and preferred language will be used to support communication, translation, complaints, and messages inside the system.',

        'sections' => [
            'personal' => 'Personal Information',
            'work' => 'Work Information',
            'account' => 'Account Settings',
        ],

        'cancel' => 'Cancel',
        'save' => 'Save Worker',
        'save_and_add_another' => 'Save and Add Another',
    ],
    'form' => [
        'name' => 'Worker Name',
        'name_placeholder' => 'Example: Ahmed Mohamed Ali',

        'phone' => 'Phone Number',
        'email' => 'Email Address',

        'iqama_number' => 'ID / Iqama Number',
        'iqama_placeholder' => 'Enter ID or Iqama number',

        'nationality' => 'Nationality',
        'choose_nationality' => 'Choose Nationality',

        'prefered_language' => 'Preferred Language',
        'choose_prefered_language' => 'Choose Preferred Language',

        'image' => 'Worker Photo',
        'image_upload' => 'Upload a clear worker photo',
        'image_uploaded' => 'Image selected successfully',

        'company' => 'Company',
        'choose_company' => 'Choose Company',

        'position' => 'Job Position',
        'position_placeholder' => 'Example: Warehouse Worker / Driver / Cleaner',

        'status' => 'Worker Status',

        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'choose_position' => 'Choose Job Position',
        'image_change' => 'Change Worker Photo',
        'password_optional' => 'Leave empty if you do not want to change it',
    ],

    'loading' => [
        'saving' => 'Saving...',
    ],

    // lang/en/workers.php

    'show' => [
        'page_title' => 'Worker Details',
        'breadcrumb_current' => 'Worker Details',
        'title' => 'Worker Details',
        'subtitle' => 'View the worker’s basic details, company, nationality, and preferred language.',
        'back' => 'Back to Workers',
        'worker_id' => 'Worker ID',
        'created_at' => 'Created At',
        'updated_at' => 'Last Updated',

        'sections' => [
            'personal' => 'Personal Information',
            'work' => 'Work Information',
            'system' => 'System Information',
        ],
    ],

    // lang/en/workers.php

    'edit' => [
        'page_title' => 'Edit Worker',
        'breadcrumb_current' => 'Edit Worker',
        'title' => 'Edit Worker Details',
        'subtitle' => 'Edit worker details and update company, nationality, and preferred language.',
        'back' => 'Back to Workers',
        'show_worker' => 'View Worker Details',
        'side_text' => 'You can update the worker details, company, job position, and preferred language.',
        'notice' => 'Leave the password fields empty if you do not want to change the current password.',
        'notes_title' => 'Important Notes',
        'notes' => [
            'password_optional' => 'Password is optional while editing.',
            'image_optional' => 'Leave the image empty if you do not want to change it.',
            'company_active' => 'The worker can only be linked to an active company.',
        ],
        'sections' => [
            'personal' => 'Personal Information',
            'work' => 'Work Information',
            'account' => 'Account Settings',
        ],
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
    ],

];
