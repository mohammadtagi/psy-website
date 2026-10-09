<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlanStage extends Model
{
        use HasFactory;

    protected $fillable = [
                'treatment_plan_id',
                'stage_number',
                'title',
                'goal',
                'estimated_sessions',
            ];

    public function treatmentPlan(): BelongsTo
    {
                return $this->belongsTo(TreatmentPlan::class);
    }

    public function clinicalNotes(): HasMany
    {
                return $this->hasMany(ClinicalNote::class);
    }
    protected function casts(): array
    {
        return [
            'treatment_plan_id' => 'integer',
            'stage_number' => 'integer',
            'estimated_sessions' => 'integer',
        ];
    }

}
