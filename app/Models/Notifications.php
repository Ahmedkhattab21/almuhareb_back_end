<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;


class Notifications extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'actor_type',
        'actor_id',
        'entity_type',
        'entity_id',
        'type',
        'title',
        'body',
        'url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Notifications $notification) {
            if (! $notification->getKey()) {
                $notification->{$notification->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForRecipient(Builder $query, Model $recipient): Builder
    {
        return $query
            ->where('recipient_type', get_class($recipient))
            ->where('recipient_id', $recipient->getKey());
    }

    public function markAsRead(): bool
    {
        if ($this->read_at) {
            return true;
        }

        return $this->update([
            'read_at' => now(),
        ]);
    }

}
