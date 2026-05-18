<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

        protected $fillable = [
        'ticket_id',
        'sender_type',
        'sender_id',
        'message_order',
        'message_original',
        'message_translated',
        'original_language',
        'translated_language',
        'is_ai_generated',
        'read_at',
    ];

    protected $casts = [
        'ticket_id' => 'integer',
        'sender_id' => 'integer',
        'message_order' => 'integer',
        'is_ai_generated' => 'boolean',
        'read_at' => 'datetime',
    ];


    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id');
    }

    public function aiSuggestions()
    {
        return $this->hasMany(AiSuggestion::class, 'message_id');
    }

    public function latestAiSuggestion()
    {
        return $this->hasOne(AiSuggestion::class, 'message_id')
            ->latestOfMany();
    }

    public function isFromWorker(): bool
    {
        return $this->sender_type === 'worker';
    }

    public function isFromLawyer(): bool
    {
        return $this->sender_type === 'lawyer';
    }

    public function isFromCompany(): bool
    {
        return $this->sender_type === 'company';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function isFromAi(): bool
    {
        return $this->sender_type === 'ai' || $this->is_ai_generated;
    }
}
