<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_record_id',
        'appointment_id',
                'treatment_plan_id',
                'treatment_plan_stage_id',
                'session_type',
        'session_at',
        'summary',
        'client_condition',
        'interventions',
        'homework_and_next_plan',
        'private_note',
    ];

    public const SESSION_TYPE_PLAN = 'plan';

    public const SESSION_TYPE_FREE = 'free';

    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function treatmentPlanStage(): BelongsTo
    {
            return $this->belongsTo(TreatmentPlanStage::class);
    }

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    protected function casts(): array
    {
        return [
            'clinical_record_id' => 'integer',
            'appointment_id' => 'integer',
            'treatment_plan_id' => 'integer',
            'treatment_plan_stage_id' => 'integer',
            'session_at' => 'datetime',
        ];
    }

}
