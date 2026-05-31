<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lawyer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'lawyers';

    protected $fillable = [
        'admin_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'preferred_language',
        'avatar',
        'rating',
        'avg_response_minutes',
        'active_cases_count',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'avg_response_minutes' => 'integer',
        'active_cases_count' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'lawyers_categories',
            'lawyer_id',
            'company_id'
        )->withPivot('category_id')->withTimestamps()->distinct();
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'lawyers_categories',
            'lawyer_id',
            'category_id'
        )->withPivot('company_id')->withTimestamps()->distinct();
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class, 'lawyer_id');
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
