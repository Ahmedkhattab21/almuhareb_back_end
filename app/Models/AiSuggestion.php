<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    use HasFactory;

        protected $fillable = [
        'message_id',
        'suggested_reply',
        'suggested_language',
        'status',
        'used_at',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'used_at' => 'datetime',
    ];


    public function message()
    {
        return $this->belongsTo(TicketMessage::class, 'message_id');
    }

    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'used_at' => now(),
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    public function markAsEdited(): void
    {
        $this->update([
            'status' => 'edited',
            'used_at' => now(),
        ]);
    }
}
