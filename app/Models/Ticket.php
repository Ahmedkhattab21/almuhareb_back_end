<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

      protected $fillable = [
        'worker_id',
        'company_id',
        'lawyer_id',
        'title',
        'status',
        'priority',
        'last_message_preview',
        'last_message_at',
        'closed_at',
    ];

    protected $casts = [
        'worker_id' => 'integer',
        'company_id' => 'integer',
        'lawyer_id' => 'integer',
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

     public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')
            ->orderBy('message_order')
            ->orderBy('id');
    }

    public function latestMessage()
    {
        return $this->hasOne(TicketMessage::class, 'ticket_id')
            ->latestOfMany();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForLawyer($query, int $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

}
