<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ClinicalNote;

class Appointment extends Model
{
    use HasFactory;

    public const SESSION_TYPE_ONLINE = 'online';

    public const SESSION_TYPE_IN_PERSON = 'in_person';

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_CONFIRMED = 'confirmed';
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function successfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->where('status', Payment::STATUS_PAID)
            ->latestOfMany();
    }

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'psychologist_id',
        'client_id',
        'availability_id',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'session_type',
        'status',
        'amount',
        'hold_expires_at',
        'meeting_url',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'hold_expires_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'duration_minutes' => 'integer',
            'amount' => 'integer',
        ];
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    public function clinicalNote(): HasOne

    {

        return $this->hasOne(ClinicalNote::class);

    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_CONFIRMED,
        ]);
    }
}
