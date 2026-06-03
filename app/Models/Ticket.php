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
        'category_id',
        'lat',
        'long',
        'title',
        'title_original',
        'title_translated',
        'title_original_language',
        'title_translated_language',
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
        'category_id' => 'integer',
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')
            ->orderBy('message_order')
            ->orderBy('id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'ticket_id');
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
