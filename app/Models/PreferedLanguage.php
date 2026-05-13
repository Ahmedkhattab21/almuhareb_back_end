<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferedLanguage extends Model
{
    use HasFactory;

        protected $table = 'prefered_languages';

    protected $fillable = [
        'prefered_language',
        'code',
        'status',
    ];

        public function nationalitiesPreferedLanguages()
    {
        return $this->hasMany(
            NationalitiesPreferedLanguage::class,
            'prefered_language_id'
        );
    }
}
