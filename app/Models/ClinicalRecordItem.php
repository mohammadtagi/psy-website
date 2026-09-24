<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalRecordItem extends Model
{
    use HasFactory;

    public const TYPE_MEDICAL_HISTORY = 'medical_history';

    public const TYPE_MEDICATION = 'medication';

    public const TYPE_QUOTE = 'quote';

    public const TYPE_ASSESSMENT = 'assessment';

    public const TYPE_LIFE_EVENT = 'life_event';

    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'clinical_record_id',
        'type',
        'title',
        'content',
        'occurred_on',
        'metadata',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
            'metadata' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
