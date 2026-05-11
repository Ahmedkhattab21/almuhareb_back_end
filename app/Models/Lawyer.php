<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

    protected $table = 'lawyers';

    public $timestamps = false;

    protected $fillable = [

        'admin_id',
        'name',
        'email',
        'phone',
        'license_number',
        'specialization',
        'avatar',
        'password',
        'status',
        'preferred_language',
        'rating',
        'avg_response_minutes',
        'active_cases_count',
        'created_by',
    ];

    protected $hidden = [
        'password',
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
