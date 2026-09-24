<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClinicalRecord extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'client_id',
        'gender',
        'family_status_and_living_conditions',
        'occupation',
        'employment_status',
        'presenting_problem',
        'problem_onset',
        'previous_treatment',
        'current_medications',
        'important_medical_history',
        'suicide_risk',
        'self_harm_risk',
        'harm_to_others_risk',
        'overall_risk_level',
        'safety_actions',
        'important_life_history',
        'important_relationships_and_support',
        'values_and_personal_resources',
        'clinical_observations',
        'clinical_formulation',
        'treatment_approach',
        'treatment_goals',
        'treatment_stage',
        'treatment_plan_and_schedule',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClinicalRecordItem::class)
            ->orderBy('sort_order');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ClinicalAttachment::class)
            ->latest();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
}
