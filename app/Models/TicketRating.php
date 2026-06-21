<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'worker_id',
        'company_id',
        'lawyer_id',
        'rating',
        'message',
    ];

    protected $casts = [
        'ticket_id' => 'integer',
        'worker_id' => 'integer',
        'company_id' => 'integer',
        'lawyer_id' => 'integer',
        'rating' => 'integer',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

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
}
