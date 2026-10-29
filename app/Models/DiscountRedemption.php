<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountRedemption extends Model
{
    protected $fillable = [
        'discount_code_id',
        'appointment_id',
        'payment_id',
        'client_id',
        'code',
        'original_amount',
        'discount_amount',
        'final_amount',
    ];

    protected function casts(): array
    {
        return [
            'original_amount' => 'integer',
            'discount_amount' => 'integer',
            'final_amount' => 'integer',
        ];
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
