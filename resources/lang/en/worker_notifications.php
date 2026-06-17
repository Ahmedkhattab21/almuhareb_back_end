<?php

return [
    'types' => [
        'company_news_created' => [
            'title' => 'New company news',
            'body' => ':news_title',
        ],
        'worker_created' => [
            'title' => 'Your account has been created',
            'body' => 'Worker account :worker_name has been added to the system.',
        ],
        'worker_updated' => [
            'title' => 'Your profile has been updated',
            'body' => 'Worker profile :worker_name has been updated.',
        ],
        'worker_deleted' => [
            'title' => 'Worker deleted',
            'body' => 'Worker :worker_name has been deleted from the system.',
        ],
        'ticket_created' => [
            'title' => 'New ticket created',
            'body' => 'Your ticket #:ticket_id has been created successfully.',
        ],
        'ticket_message_created' => [
            'title' => 'New ticket reply',
            'body' => 'There is a new reply on ticket #:ticket_id.',
        ],
        'ticket_status_updated' => [
            'title' => 'Ticket status updated',
            'body' => 'Ticket #:ticket_id status has been updated.',
        ],
        'ticket_closed' => [
            'title' => 'Ticket closed',
            'body' => 'Ticket #:ticket_id has been closed.',
        ],
    ],
];
