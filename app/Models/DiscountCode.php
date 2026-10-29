<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'type',
        'value',
        'client_id',
        'expires_at',
        'is_active',
        'usage_limit',
        'per_client_limit',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'client_id' => 'integer',
            'expires_at' => 'immutable_datetime',
            'is_active' => 'boolean',
            'usage_limit' => 'integer',
            'per_client_limit' => 'integer',
        ];
    }

    public static function normalizeCode(string $code): string
    {
        return strtoupper(strtr(trim($code), [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3',
            '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7',
            '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3',
            '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7',
            '٨' => '8', '٩' => '9',
        ]));
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(DiscountRedemption::class);
    }

    public function discountFor(int $amount): int
    {
        $amount = max(0, $amount);

        $discount = match ($this->type) {
            self::TYPE_PERCENT => intdiv($amount * $this->value, 100),
            self::TYPE_FIXED => $this->value,
            default => throw new \LogicException('Invalid discount type.'),
        };

        return min($amount, max(0, $discount));
    }
}
