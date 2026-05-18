<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerLoginOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'phone',
        'code_hash',
        'attempts',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'worker_id' => 'integer',
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }
}
