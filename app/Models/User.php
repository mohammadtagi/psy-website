<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ClinicalRecord;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'birth_date',
        'mobile',
        'email',
        'password',
        'role',
                'created_by_psychologist_id',

    ];


    public function createdByPsychologist(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'created_by_psychologist_id',
        );
    }

    public function manuallyCreatedClients(): HasMany
    {
        return $this->hasMany(
            self::class,
            'created_by_psychologist_id',
        )->where('role', self::ROLE_CLIENT);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public const ROLE_CLIENT = 'client';

    public const ROLE_PSYCHOLOGIST = 'psychologist';
    public function clinicalRecord(): HasOne
    {
        return $this->hasOne(ClinicalRecord::class, 'client_id');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class, 'client_id');
    }

    public function psychologistTreatmentPlans(): HasMany
    {
            return $this->hasMany(TreatmentPlan::class, 'psychologist_id');
    }
    public const ROLE_ADMIN = 'admin';

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isPsychologist(): bool
    {
        return $this->role === self::ROLE_PSYCHOLOGIST;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array(
            $this->role,
            (array) $roles,
            true
        );
    }


    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class, 'psychologist_id');
    }
    public function psychologistAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'psychologist_id');
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }


    public function clientAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
                        'created_by_psychologist_id' => 'integer',

        ];
    }



}
