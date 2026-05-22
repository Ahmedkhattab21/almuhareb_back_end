<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppPage extends Model
{
    use HasFactory;

    public const TYPE_ABOUT_APP = 'about_app';
    public const TYPE_PRIVACY_POLICY = 'privacy_policy';

    protected $fillable = [
        'created_by_admin_id',
        'type',
        'title',
        'content',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_ABOUT_APP,
            self::TYPE_PRIVACY_POLICY,
        ];
    }

    public function adminCreator()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
