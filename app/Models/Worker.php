<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;


       protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'password',
        'iqama_number',
   'position_id',
        'image',
        'created_by',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

       public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(Company::class, 'created_by');
    }
    public function nationality()
{
    return $this->belongsTo(Nationality::class, 'nationality_id');
}
public function nationalityPreferredLanguage()
{
    return $this->hasOne(NationalitiesPreferedLanguage::class, 'worker_id');
}

public function position()
{
    return $this->belongsTo(Position::class, 'position_id');
}
}
