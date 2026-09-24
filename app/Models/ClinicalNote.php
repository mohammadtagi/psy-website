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
        'session_at',
        'summary',
        'client_condition',
        'interventions',
        'homework_and_next_plan',
        'private_note',
    ];

    protected function casts(): array
    {
        return [
            'session_at' => 'datetime',
        ];
    }

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
