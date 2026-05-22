<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyNews extends Model
{
    use HasFactory;

    protected $table = 'company_news';

    protected $fillable = [
        'company_id',
        'created_by_admin_id',
        'created_by_company_id',
        'title',
        'image',
        'description',
    ];

    protected $appends = [
        'image_url',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function adminCreator()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function companyCreator()
    {
        return $this->belongsTo(Company::class, 'created_by_company_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? url('/storage/' . ltrim($this->image, '/')) : null;
    }
}
