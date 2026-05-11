<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
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
}
