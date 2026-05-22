<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    use HasFactory;


        protected $fillable = [
        'lawyer_id',
        'company_name',
        'email',
        'password',
        'phone',
        'tax_number',
        'address',
        'status',
        'created_by',
    ];


   protected $hidden = [
        'password',
    ];

       public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function workers()
    {
        return $this->hasMany(Worker::class, 'company_id');
    }

    public function scopeAssignedToLawyer($query, $lawyerId)
    {
        return $query->where('lawyer_id', $lawyerId);
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class, 'company_id');
}

public function news()
{
    return $this->hasMany(CompanyNews::class, 'company_id');
}

public function notifications(): MorphMany
{
    return $this->morphMany(Notifications::class, 'recipient')
        ->latest();
}

public function unreadNotifications(): MorphMany
{
    return $this->notifications()
        ->whereNull('read_at');
}
}
