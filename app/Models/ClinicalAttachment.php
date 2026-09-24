<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_record_id',
        'description',
        'category',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'document_date',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'size' => 'integer',
        ];
    }

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
