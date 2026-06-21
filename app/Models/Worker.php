<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Worker extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    protected $table = 'workers';

    protected $fillable = [
        'company_id',
        'created_by',
        'operating_company',

        'name',
        'email',
        'phone',
        'iqama_number',
        'residency_number',
        'national_id',
        'status',
        'image',
        'avatar',

        'position_id',
        'city_id',
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
        'fcm_token',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',

        'position_id' => 'integer',
        'city_id' => 'integer',
        'nationality_id' => 'integer',

        'prefered_language_id' => 'integer',
        'preferred_language_id' => 'integer',
        'language_id' => 'integer',

        'nationality_preferred_language_id' => 'integer',
        'nationality_prefered_language_id' => 'integer',

        'open_tickets_count' => 'integer',
        'tickets_count' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'worker_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'worker_id');
    }

    public function ticketMessages()
    {
        return $this->hasMany(TicketMessage::class, 'sender_id')
            ->where('sender_type', 'worker');
    }

    public function preferredLanguageCode(): ?string
    {
        $this->loadMissing([
            'preferedLanguage',
            'preferredLanguage',
            'nationalityPreferredLanguage.preferedLanguage',
            'nationalityPreferredLanguage.preferredLanguage',
        ]);

        $code = $this->preferred_language
            ?? $this->preferedLanguage?->code
            ?? $this->preferredLanguage?->code
            ?? $this->nationalityPreferredLanguage?->preferedLanguage?->code
            ?? $this->nationalityPreferredLanguage?->preferredLanguage?->code
            ?? $this->language
            ?? $this->prefered_language;

        $code = is_string($code) ? trim($code) : null;

        return $code !== '' && strlen($code) <= 10 ? $code : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForLawyer($query, $lawyerId)
    {
        return $query->whereHas('company', function ($companyQuery) use ($lawyerId) {
            $companyQuery->where('lawyer_id', $lawyerId);
        });
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
