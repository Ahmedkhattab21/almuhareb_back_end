<?php

return [
    'page_title' => 'Consultants Management',

    'breadcrumb_parent' => 'Management',
    'breadcrumb_current' => 'Consultants',

    'title' => 'Consultants Management',
    'subtitle' => 'Manage, monitor, and improve the legal consultants network.',

    'add_new' => 'Add New Consultant',

    'stats' => [
        'total' => 'Total Consultants',
        'active' => 'Active Consultants',
        'avg_rating' => 'Average Rating',

        'pending' => 'Pending',
        'pending_hint' => 'Consultants waiting for activation',
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
        'title' => 'Consultants Directory',
        'showing' => 'Showing',
        'from' => 'from',
        'lawyer' => 'consultant',
        'id' => 'ID',
        'name' => 'Consultant Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'status' => 'Status',
        'license_number' => 'License Number',
        'license_short' => 'License',
        'specialization' => 'Specialization',
        'rating_from_5' => 'Rating out of 5',
        'cases_count' => 'Consultations Count',
        'actions' => 'Actions',
        'lawyer_role' => 'Consultant',
        'not_defined' => 'Not defined',
        'empty' => 'No consultants found',
        'cases' => 'Cases',
        'response' => 'Response',
        'rating' => 'Rating',
        'preferred_language' => 'Preferred Language',

    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this consultant?',
        'show' => 'Show',
    ],

    'messages' => [
        'created' => 'Consultant created successfully',

        'deleted' => 'Consultant deleted successfully',
        'create_failed' => 'Something went wrong while creating the consultant. Please try again.',
        'updated' => 'Consultant data has been updated successfully.',
        'update_failed' => 'Something went wrong while updating the consultant. Please try again.',

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
        'page_title' => 'Add New Consultant',
        'breadcrumb_current' => 'Add Consultant',
        'title' => 'Add New Consultant',
        'subtitle' => 'Add a new consultant to the system and define login details and preferred language.',
        'back' => 'Back to Consultants',
        'side_title' => 'Important Note',
        'side_text' => 'The consultant can later be assigned to companies and follow up their consultations.',
        'notice' => 'After adding the consultant, you can assign them to registered companies.',
        'cancel' => 'Cancel',
        'save' => 'Save Consultant',
        'save_and_add_another' => 'Save and Add Another Consultant',
        'no_active_companies' => 'There are no active companies available for assignment.',
    'companies_hint' => 'Select only active companies to assign to this consultant.',
        'categories_hint' => 'Select the case types this consultant can handle.',
        'no_active_categories' => 'There are no active case types available.',

        'sections' => [
            'personal' => 'Personal Information',
            'system' => 'System Settings',
            'account' => 'Account Settings',

            'platform' => 'Platform Work Data',
                    'categories' => 'Assigned Case Types',
                    'companies' => 'Assign Consultant to Companies',


        ],
    ],

    'form' => [
        'name' => 'Consultant Name',
        'name_placeholder' => 'Example: Mohammed Al-Saadi',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'preferred_language' => 'Preferred Language',
        'status' => 'Consultant Status',
        'categories' => 'Case Types',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',

        'avatar' => 'Consultant Photo',
        'avatar_upload' => 'Upload a clear consultant photo',
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
        'page_title' => 'Consultant Details',
        'breadcrumb_current' => 'Consultant Details',
        'title' => 'Consultant Details',
        'subtitle' => 'View consultant information, performance, and related companies.',
        'edit' => 'Edit Consultant Data',
        'related_companies' => 'Related Companies',
        'back' => 'Back',
        'lawyer_role' => 'Consultant in the system',
        'rating' => 'Rating',
        'created_at' => 'Created At',
        'updated_at' => 'Last Update',
        'personal_info' => 'Personal Information',
        'performance' => 'Consultant Performance',
        'system_info' => 'System Information',
        'created_by' => 'Created By',
        'admin' => 'Responsible Admin',
        'companies_title' => 'Related Companies',
        'case_categories' => 'Case Types',
        'no_case_categories' => 'No case types are assigned to this consultant.',
        'latest_tickets' => 'Latest Consultations',
        'view_all' => 'View All',
        'no_companies' => 'No companies are linked to this consultant.',
        'no_tickets' => 'No consultations are linked to this consultant.',
        'ticket' => 'Consultation',

        'stats' => [
            'companies' => 'Related Companies',
            'workers' => 'Workers',
            'total_tickets' => 'All Consultations',
            'open_tickets' => 'All Consultations',
            'active_cases' => 'Open Consultations',
            'response' => 'Average Response',
        ],
    ],

    'edit' => [
        'page_title' => 'Edit Consultant',
        'breadcrumb_current' => 'Edit Consultant',
        'title' => 'Edit Consultant Data',
        'subtitle' => 'Update the consultant basic information and account status.',
        'back' => 'Back to Consultants',
        'show_lawyer' => 'View Consultant Details',
        'last_update' => 'Last Update:',
        'created_at' => 'Created At',
        'notes_title' => 'Important Notes',
        'note_password' => 'Leave the password empty if you do not want to change it.',
        'note_status' => 'Changing consultant status affects their availability in the system.',
        'note_language' => 'The consultant default language is fixed to Arabic and cannot be changed here.',
        'password_hint' => 'Leave this field empty to keep the current password.',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
        'save_and_show' => 'Save and Back to Details',
        'categories_hint' => 'Update the case types assigned to this consultant.',
        'no_active_categories' => 'There are no active case types available.',
        'suspend_lawyer' => 'Suspend Consultant',
        'confirm_suspend' => 'Are you sure you want to suspend this consultant?',
'companies_hint' => 'Select only active companies to assign to this consultant.',
'no_active_companies' => 'There are no active companies available for assignment.',
        'sections' => [
            'personal' => 'Personal Information',
            'platform' => 'Platform Work Settings',
            'account' => 'Account Settings',
            'companies' => 'Assign Consultant to Companies',
        ],
    ],

];
