<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
    ];

    protected $appends = [
        'url',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'file_size' => 'integer',
    ];
    public function message()
    {
        return $this->belongsTo(TicketMessage::class, 'message_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
