<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class OtpCode extends Model
{
    protected $fillable = [
        'mobile',
        'code_hash',
        'purpose',
        'attempts',
        'expires_at',
        'consumed_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'consumed_at' => 'datetime',
            'revoked_at'  => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function matches(string $code): bool
    {
        return Hash::check($code, $this->code_hash);
    }
}
