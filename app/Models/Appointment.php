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
    public const BOOKING_SOURCE_WEBSITE = 'website';

    public const BOOKING_SOURCE_MANUAL = 'manual';

    public const MANUAL_PAYMENT_EXTERNAL = 'paid_outside';

    public const MANUAL_PAYMENT_FREE = 'free';
    public function isManuallyBooked(): bool
    {
        return $this->booking_source === self::BOOKING_SOURCE_MANUAL;
    }

    public function bookingSourceLabel(): string
    {
        return match ($this->booking_source) {
            self::BOOKING_SOURCE_WEBSITE => 'رزرو سایت',
            self::BOOKING_SOURCE_MANUAL => 'ثبت دستی مشاور',
            default => 'نامشخص',
        };
    }

    public function manualPaymentMethodLabel(): ?string
    {
        if (! $this->isManuallyBooked()) {
            return null;
        }

        return match ($this->manual_payment_method) {
            self::MANUAL_PAYMENT_EXTERNAL => 'پرداخت‌شده خارج از سایت',
            self::MANUAL_PAYMENT_FREE => 'رایگان',
            default => 'نامشخص',
        };
    }

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment): void {
            do {
                $code = (string) random_int(1000000, 9999999);
            } while (
                static::query()
                    ->where('tracking_code', $code)
                    ->exists()
            );

            $appointment->tracking_code = $code;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'tracking_code';
    }

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


    public const THERAPY_TYPE_FREE = 'free';

    public const THERAPY_TYPE_PLAN = 'plan';

    public const ATTENDANCE_PENDING = 'pending';

    public const ATTENDANCE_HELD = 'held';

    public const ATTENDANCE_ABSENT = 'absent';


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
                'client_note',

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
            'treatment_plan_id' => 'integer',
            'treatment_plan_stage_id' => 'integer',

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

    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function treatmentPlanStage(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanStage::class);
    }

    public function clientSessionNotes(): HasMany
    {
        return $this->hasMany(ClientSessionNote::class);
    }

    /**
     * فقط جلسات تأییدشده‌ای که مشاور برگزاری آن‌ها را ثبت کرده است.
     */
    public function scopeHeld(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_CONFIRMED)
            ->where('attendance_status', self::ATTENDANCE_HELD);
    }

    /**
     * جلسات قابل شمارش در پیشرفت مسیر درمان.
     */
    public function scopeCountableForTreatment(Builder $query): Builder
    {
        return $query
            ->held()
            ->where('therapy_session_type', self::THERAPY_TYPE_PLAN)
            ->whereNotNull('treatment_plan_id')
            ->whereNotNull('treatment_plan_stage_id');
    }

    public function sessionStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_CANCELLED => 'لغوشده',
            self::STATUS_PENDING_PAYMENT => 'در انتظار پرداخت',

            self::STATUS_CONFIRMED => match ($this->attendance_status) {
                self::ATTENDANCE_HELD => 'برگزارشده',
                self::ATTENDANCE_ABSENT => 'عدم حضور مراجع',
                default => 'در انتظار برگزاری',
            },

            // سازگاری موقت با کدهای قدیمی تا تکمیل پچ‌ها.
            self::STATUS_COMPLETED => 'برگزارشده',
            self::STATUS_NO_SHOW => 'عدم حضور مراجع',

            default => 'وضعیت نامشخص',
        };
    }

}
