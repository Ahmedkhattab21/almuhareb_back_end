<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

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
         return $this->hasMany(Company::class, 'lawyer_id');
     }
}
