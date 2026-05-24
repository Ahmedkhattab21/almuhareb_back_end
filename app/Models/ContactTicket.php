<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTicket extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'status',
        'read_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
