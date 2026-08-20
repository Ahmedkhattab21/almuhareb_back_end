<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'admin_id',
        'name',
        'status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function lawyers()
    {
        return $this->belongsToMany(
            Lawyer::class,
            'lawyers_categories',
            'category_id',
            'lawyer_id'
        )->withPivot('company_id')->withTimestamps()->distinct();
    }

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'lawyers_categories',
            'category_id',
            'company_id'
        )->withPivot('lawyer_id')->withTimestamps()->distinct();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }
}
