<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',

        'name',
        'email',
        'phone',
        'password',
        'iqama_number',
        'residency_number',
        'national_id',
        'status',
        'image',
        'avatar',

        'position_id',
        'position',
        'job_title',

        'nationality_id',
        'nationality',

        'prefered_language_id',
        'preferred_language_id',
        'language_id',
        'prefered_language',
        'preferred_language',
        'language',

        'nationality_preferred_language_id',
        'nationality_prefered_language_id',

        'open_tickets_count',
        'tickets_count',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'position_id' => 'integer',
        'nationality_id' => 'integer',
        'prefered_language_id' => 'integer',
        'preferred_language_id' => 'integer',
        'language_id' => 'integer',
        'nationality_preferred_language_id' => 'integer',
        'nationality_prefered_language_id' => 'integer',
        'open_tickets_count' => 'integer',
        'tickets_count' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(Company::class, 'created_by');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function preferedLanguage()
    {
        return $this->belongsTo(PreferedLanguage::class, 'prefered_language_id');
    }

    public function preferredLanguage()
    {
        return $this->belongsTo(PreferedLanguage::class, 'preferred_language_id');
    }

   public function nationalityPreferredLanguage()
{
    return $this->hasOne(NationalitiesPreferedLanguage::class, 'worker_id');
}

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->whereHas('company', function ($companyQuery) use ($lawyerId) {
            $companyQuery->where('lawyer_id', $lawyerId);
        });
    }
}
