<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const GATEWAY_ZARINPAL = 'zarinpal';

    public const STATUS_INITIATED = 'initiated';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'appointment_id',
        'client_id',
        'amount',
        'gateway',
        'status',
        'authority',
        'ref_id',
        'gateway_message',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_INITIATED,
                self::STATUS_PENDING,
            ],
            true
        );
    }

    public function isFinal(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PAID,
                self::STATUS_FAILED,
                self::STATUS_CANCELLED,
            ],
            true
        );
    }
}
