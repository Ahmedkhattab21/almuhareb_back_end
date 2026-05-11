<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;
       protected $fillable = [
        'nationality',
        'preferred_language',
        'status',
    ];

    public function workers()
    {
        return $this->hasMany(Worker::class, 'nationality_id');
    }
}
