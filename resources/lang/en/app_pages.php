<?php

return [
    'page_title' => 'App Content',
    'title' => 'App Content',
    'subtitle' => 'Manage the About App and Privacy Policy content shown in the worker app.',
    'types' => [
        'about_app' => 'About App',
        'privacy_policy' => 'Privacy Policy',
    ],
    'create' => [
        'title' => 'Add App Page',
        'subtitle' => 'Create content for one of the worker app information pages.',
        'no_available_types' => 'All app pages have already been created. You can edit or delete an existing page.',
    ],
    'edit' => [
        'title' => 'Edit App Page',
        'subtitle' => 'Update the content shown inside the worker app.',
    ],
    'form' => [
        'type' => 'Page Type',
        'select_type' => 'Select page type',
        'title' => 'Title',
        'title_placeholder' => 'Write the page title',
        'content' => 'Content',
        'content_placeholder' => 'Write the full page content',
    ],
    'filters' => [
        'search' => 'Search by title or content...',
        'apply' => 'Apply',
        'reset' => 'Reset',
    ],
    'actions' => [
        'create' => 'Add Content',
        'save' => 'Save Content',
        'update' => 'Update Content',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'back' => 'Back',
        'confirm_delete' => 'Are you sure you want to delete this page?',
    ],
    'messages' => [
        'created' => 'App page created successfully.',
        'updated' => 'App page updated successfully.',
        'deleted' => 'App page deleted successfully.',
    ],
    'empty' => 'No app pages found.',
];
