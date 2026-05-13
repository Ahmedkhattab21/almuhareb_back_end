<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalitiesPreferedLanguage extends Model
{
    use HasFactory;
       protected $table = 'nationalities_prefered_language';

    protected $fillable = [
        'worker_id',
        'nationality_id',
        'prefered_language_id',
    ];

        public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function preferedLanguage()
    {
        return $this->belongsTo(PreferedLanguage::class, 'prefered_language_id');
    }
}
