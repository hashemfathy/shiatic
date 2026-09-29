<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassageTechnique extends Model
{
    use HasFactory;

    protected $fillable = [
        'massage_protocol_id',
        'order',
        'region_number',
        'region_name',
        'massage_type',
        'tool',
        'direction',
        'reps_display',
        'intensity_percent',
        'speed_percent',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'region_number' => 'integer',
        'intensity_percent' => 'integer',
        'speed_percent' => 'integer',
        'is_active' => 'boolean',
    ];

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(MassageProtocol::class, 'massage_protocol_id');
    }
}
