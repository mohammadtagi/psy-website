<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Availability extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'psychologist_id',
        'series_key',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
