<?php

return [
    'page_title' => 'Lawyers Management',

    'breadcrumb_parent' => 'Management',
    'breadcrumb_current' => 'Lawyers',

    'title' => 'Lawyers Management',
    'subtitle' => 'Manage, monitor, and improve the legal consultants network.',

    'add_new' => 'Add New Lawyer',

    'stats' => [
        'total' => 'Total Lawyers',
        'active' => 'Active Lawyers',
        'response' => 'Average Response Time',
        'avg_rating' => 'Average Rating',

        'pending' => 'Pending',
        'pending_hint' => 'Lawyers waiting for activation',
        'top_language' => 'Top Language',
        'top_language_hint' => 'Based on preferred language',
    ],

    'available' => 'Available',
    'from_5' => 'out of 5',

    'filters' => [
        'search_placeholder' => 'Search by name, email, phone, license number...',
        'all_statuses' => 'All statuses',
        'all_specializations' => 'All specializations',
        'apply' => 'Apply Filter',
        'reset' => 'Reset',
        'latest' => 'Latest',
        'highest_rating' => 'Highest rating',
        'most_cases' => 'Most cases',
        'fastest_response' => 'Fastest response',
        'all_languages' => 'All Languages',
        'id_asc' => 'Sort by ID',
        'name_asc' => 'Name Ascending',
        'name_desc' => 'Name Descending',
    ],

    'status' => [
        'active' => 'Active',
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
        'unknown' => 'Not defined',
    ],

    'table' => [
        'title' => 'Lawyers Directory',
        'showing' => 'Showing',
        'from' => 'from',
        'lawyer' => 'lawyer',
        'id' => 'ID',
        'name' => 'Lawyer Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'status' => 'Status',
        'license_number' => 'License Number',
        'license_short' => 'License',
        'specialization' => 'Specialization',
        'rating_from_5' => 'Rating out of 5',
        'cases_count' => 'Cases Count',
        'response_time' => 'Response Time',
        'actions' => 'Actions',
        'lawyer_role' => 'Lawyer',
        'not_defined' => 'Not defined',
        'empty' => 'No lawyers found',
        'cases' => 'Cases',
        'response' => 'Response',
        'rating' => 'Rating',
        'preferred_language' => 'Preferred Language',

    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this lawyer?',
        'show' => 'Show',
    ],

    'messages' => [
        'created' => 'Lawyer created successfully',

        'deleted' => 'Lawyer deleted successfully',
        'create_failed' => 'Something went wrong while creating the lawyer. Please try again.',
        'updated' => 'Lawyer data has been updated successfully.',
        'update_failed' => 'Something went wrong while updating the lawyer. Please try again.',

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

    'create' => [
        'page_title' => 'Add New Lawyer',
        'breadcrumb_current' => 'Add Lawyer',
        'title' => 'Add New Lawyer',
        'subtitle' => 'Add a new lawyer to the system and define login details and preferred language.',
        'back' => 'Back to Lawyers',
        'side_title' => 'Important Note',
        'side_text' => 'The lawyer can later be assigned to companies and follow up their complaints.',
        'notice' => 'After adding the lawyer, you can assign them to registered companies.',
        'cancel' => 'Cancel',
        'save' => 'Save Lawyer',
        'save_and_add_another' => 'Save and Add Another Lawyer',
        'no_active_companies' => 'There are no active companies available for assignment.',
    'companies_hint' => 'Select only active companies to assign to this lawyer.',

        'sections' => [
            'personal' => 'Personal Information',
            'system' => 'System Settings',
            'account' => 'Account Settings',

            'platform' => 'Platform Work Data',
                    'companies' => 'Assign Lawyer to Companies',


        ],
    ],

    'form' => [
        'name' => 'Lawyer Name',
        'name_placeholder' => 'Example: Mohammed Al-Saadi',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'preferred_language' => 'Preferred Language',
        'status' => 'Lawyer Status',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',

        'avatar' => 'Lawyer Photo',
        'avatar_upload' => 'Upload a clear lawyer photo',
        'avatar_uploaded' => 'Image selected successfully',

        'new_password' => 'New Password',

    ],

    'languages' => [
        'ar' => 'Arabic',
        'en' => 'English',
        'ur' => 'Urdu',
        'fil' => 'Filipino',
    ],

    'loading' => [
        'saving' => 'Saving...',
    ],

    'show' => [
        'page_title' => 'Lawyer Details',
        'breadcrumb_current' => 'Lawyer Details',
        'title' => 'Lawyer Details',
        'subtitle' => 'View lawyer information, performance, and related companies.',
        'edit' => 'Edit Lawyer Data',
        'related_companies' => 'Related Companies',
        'back' => 'Back',
        'lawyer_role' => 'Lawyer in the system',
        'rating' => 'Rating',
        'created_at' => 'Created At',
        'updated_at' => 'Last Update',
        'personal_info' => 'Personal Information',
        'performance' => 'Lawyer Performance',
        'system_info' => 'System Information',
        'created_by' => 'Created By',
        'admin' => 'Responsible Admin',
        'companies_title' => 'Related Companies',
        'latest_tickets' => 'Latest Tickets',
        'view_all' => 'View All',
        'no_companies' => 'No companies are linked to this lawyer.',
        'no_tickets' => 'No tickets are linked to this lawyer.',
        'ticket' => 'Ticket',

        'stats' => [
            'companies' => 'Related Companies',
            'workers' => 'Workers',
            'open_tickets' => 'Open Tickets',
            'active_cases' => 'Active Cases',
            'response' => 'Average Response',
        ],
    ],

    'edit' => [
        'page_title' => 'Edit Lawyer',
        'breadcrumb_current' => 'Edit Lawyer',
        'title' => 'Edit Lawyer Data',
        'subtitle' => 'Update the lawyer basic information and account status.',
        'back' => 'Back to Lawyers',
        'show_lawyer' => 'View Lawyer Details',
        'last_update' => 'Last Update:',
        'created_at' => 'Created At',
        'notes_title' => 'Important Notes',
        'note_password' => 'Leave the password empty if you do not want to change it.',
        'note_status' => 'Changing lawyer status affects their availability in the system.',
        'note_language' => 'The lawyer default language is fixed to Arabic and cannot be changed here.',
        'password_hint' => 'Leave this field empty to keep the current password.',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
        'save_and_show' => 'Save and Back to Details',
        'suspend_lawyer' => 'Suspend Lawyer',
        'confirm_suspend' => 'Are you sure you want to suspend this lawyer?',
'companies_hint' => 'Select only active companies to assign to this lawyer.',
'no_active_companies' => 'There are no active companies available for assignment.',
        'sections' => [
            'personal' => 'Personal Information',
            'platform' => 'Platform Work Settings',
            'account' => 'Account Settings',
            'companies' => 'Assign Lawyer to Companies',
        ],
    ],

];
