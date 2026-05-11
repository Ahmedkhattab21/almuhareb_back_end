<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admin';

    protected $guard = 'admin';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function lawyers()
    {
        return $this->hasMany(Lawyer::class, 'admin_id');
    }

public function createdLawyers()
{
    return $this->hasMany(Lawyer::class, 'created_by');
}

public function createdCompanies()
{
    return $this->hasMany(Company::class, 'created_by');
}



}
