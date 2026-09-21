<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'mobile',
        'code_hash',
        'expires_at',
        'consumed_at',
        'attempts',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function hasReachedMaximumAttempts(): bool
    {
        return $this->attempts >= (int) config('services.otp.max_attempts', 5);
    }

    public function isUsable(): bool
    {
        return ! $this->isExpired()
            && ! $this->isConsumed()
            && ! $this->hasReachedMaximumAttempts();
    }
}
