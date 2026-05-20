<?php

return [
    'page_title' => 'Companies Management',

    'breadcrumb_parent' => 'Dashboard',
    'breadcrumb_current' => 'Companies',

    'title' => 'Companies Management',
    'subtitle' => 'Overview of registered entities and legal compliance status.',
    'add_new' => 'Add New Company',

    'stats' => [
        'total' => 'Total Companies',
         'active' => 'Active Companies',
        'active_hint' => ' activity rate',
        'open_disputes' => 'Inactive companies',
        'open_disputes_hint' => 'Requires immediate attention',
    ],

    'filters' => [
        'search_placeholder' => 'Search company, email, phone, tax number...',
        'all_statuses' => 'All Statuses',
        'all_lawyers' => 'All Lawyers',
        'latest' => 'Latest',
        'oldest' => 'Oldest',
        'name_asc' => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'apply' => 'Apply',
        'reset' => 'Reset',
        'id_asc' => 'Sort by ID',
    ],

    'status' => [
        'active' => 'Active',
        'pending' => 'Pending Review',
        'suspended' => 'Suspended',
        'unknown' => 'Unknown',
    ],

    'table' => [
        'title' => 'Companies List',
        'showing' => 'Showing',
        'from' => 'from',
        'company' => 'company',
        'id' => 'ID',
        'company_name' => 'Company',
        'email' => 'Email',
        'phone' => 'Phone',
        'tax_number' => 'Tax Number',
        'address' => 'Address',
        'lawyer' => 'Assigned Lawyer',
        'status' => 'Status',
        'actions' => 'Actions',
        'workers' => 'Workers',
        'empty' => 'No companies found',
        'not_assigned' => 'Not assigned',
        'no_address' => 'No address',
    ],

    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this company?',
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
        'created' => 'Company has been created successfully.',
        'updated' => 'Company has been updated successfully.',
        'deleted' => 'Company has been deleted successfully.',
        'create_failed' => 'Something went wrong while creating the company. Please try again.',
        'update_failed' => 'Something went wrong while updating the company. Please try again.',
        'delete_failed' => 'Something went wrong while deleting the company. Please try again.',
    ],


    'create' => [
    'page_title' => 'Add New Company',
    'breadcrumb_current' => 'Add Company',
    'title' => 'Add New Company',
    'subtitle' => 'Register a new company and assign it to a lawyer inside the system.',
    'back' => 'Back to Companies',
    'notice' => 'The company will be linked to a lawyer, and workers can be added later after creating the account.',
    'side_title' => 'Important Note',
    'side_text' => 'After saving the company, you can add workers and link them to it from the workers section.',
    'cancel' => 'Cancel',
    'save' => 'Save Company',
    'save_and_add_worker' => 'Save and Add Worker',

    'sections' => [
        'basic' => 'Basic Company Information',
        'legal' => 'Legal Assignment',
        'account' => 'Account Settings',
    ],
],

'form' => [
    'company_name' => 'Company Name',
    'company_name_placeholder' => 'Example: Al Muhareb Legal Company',
    'email' => 'Email Address',
    'phone' => 'Phone Number',
    'tax_number' => 'Tax Number',
    'tax_number_placeholder' => 'Enter tax number',
    'address' => 'Address',
    'address_placeholder' => 'Enter company address',
    'status' => 'Company Status',
    'lawyer' => 'Assigned Lawyer',
    'choose_lawyer' => 'Choose Lawyer',
    'password' => 'Password',
    'password_confirmation' => 'Password Confirmation',
    'new_password' => 'Change Password',
],
'show' => [
    'page_title' => 'Company Details',
    'breadcrumb_current' => 'Company Details',
    'title' => 'Company Details',
    'subtitle' => 'View company information, legal assignment, and linked workers.',
    'back' => 'Back',
    'edit' => 'Edit Company',
    'add_worker' => 'Add Worker',
    'company_id' => 'Company ID',
    'created_by' => 'Created By',
    'created_at' => 'Created At',
    'updated_at' => 'Last Updated',
    'lawyer_name' => 'Lawyer Name',
    'lawyer_email' => 'Lawyer Email',
    'lawyer_phone' => 'Lawyer Phone',
    'lawyer_license' => 'Lawyer License',
    'no_lawyer' => 'No lawyer has been assigned to this company yet.',
    'workers_title' => 'Company Workers',
    'no_workers' => 'No workers are linked to this company yet.',

    'stats' => [
        'workers' => 'Workers',
        'active_workers' => 'Active Workers',
        'total_tickets' => 'All Complaints',
        'assigned_lawyer' => 'Assigned Lawyer',
    ],

    'sections' => [
        'company_data' => 'Company Data',
        'legal_link' => 'Legal Assignment',
        'account' => 'Account Data',
    ],

    'worker_table' => [
        'id' => 'ID',
        'name' => 'Worker Name',
        'phone' => 'Phone',
        'nationality' => 'Nationality',
        'status' => 'Status',
    ],
],

'edit' => [
    'page_title' => 'Edit Company',
    'breadcrumb_current' => 'Edit Company',
    'title' => 'Edit Company Data',
    'subtitle' => 'Update company details, legal assignment, and account settings.',
    'back' => 'Back',
    'show_company' => 'View Company Details',
    'last_update' => 'Last update:',
    'notes_title' => 'Important Notes',
    'note_lawyer' => 'Changing the assigned lawyer may affect new complaints follow-up.',
    'note_password' => 'Leave password empty if you do not want to change it.',
    'note_suspend' => 'Suspending the company prevents company supervisors and workers from logging in.',
    'password_hint' => 'Leave it empty if you do not want to change the password.',
    'cancel' => 'Cancel',
    'save' => 'Save Changes',
    'save_and_show' => 'Save and Back to Details',
    'suspend_company' => 'Suspend Company',
    'confirm_suspend' => 'Are you sure you want to suspend this company?',

    'sections' => [
        'basic' => 'Basic Company Information',
        'legal' => 'Legal Settings',
        'account' => 'Account Settings',
    ],
],
'loading' => [
    'saving' => 'Saving...',
],
];
