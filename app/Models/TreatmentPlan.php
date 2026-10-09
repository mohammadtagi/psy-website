<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlan extends Model
{
        use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DROPPED_OUT = 'dropped_out';

    public const STATUS_REFERRED = 'referred';

    public const STAGE_ROOT_CAUSE = 1;

    public const STAGE_SKILLS_AND_ACTION = 2;

    public const STAGE_STABILIZATION = 3;

    protected $fillable = [
        'psychologist_id',
        'client_id',
        'clinical_record_id',
        'title',
        'main_problem',
        'status',
        'active_plan_guard',
        'current_stage',
        'status_reason',
    ];


    public function psychologist(): BelongsTo
    {
                return $this->belongsTo(User::class, 'psychologist_id');
    }

    public function client(): BelongsTo
    {
                return $this->belongsTo(User::class, 'client_id');
    }

    public function clinicalRecord(): BelongsTo
    {
                return $this->belongsTo(ClinicalRecord::class);
    }

    public function stages(): HasMany
    {
                return $this->hasMany(TreatmentPlanStage::class)
                        ->orderBy('stage_number');
    }

    public function clinicalNotes(): HasMany
    {
                return $this->hasMany(ClinicalNote::class);
    }

    public function isActive(): bool
    {
                return $this->status === self::STATUS_ACTIVE;
    }

    public function isArchived(): bool
    {
                return in_array($this->status, [
                        self::STATUS_COMPLETED,
                        self::STATUS_DROPPED_OUT,
                        self::STATUS_REFERRED,
                    ], true);
    }

    protected function casts(): array
    {
        return [
            'psychologist_id' => 'integer',
            'client_id' => 'integer',
            'clinical_record_id' => 'integer',
            'active_plan_guard' => 'integer',
            'current_stage' => 'integer',
        ];
    }

}
