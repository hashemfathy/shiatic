<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MassageProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_type',
        'pain_level',
        'weight_bracket',
        'name',
        'luxury_price_per_technique',
        'luxury_duration_minutes',
        'luxury_reps',
        'economy_price_per_technique',
        'economy_duration_minutes',
        'economy_reps',
        'intensity_percent',
        'speed_percent',
        'is_active',
    ];

    protected $casts = [
        'luxury_price_per_technique' => 'decimal:2',
        'luxury_duration_minutes' => 'decimal:2',
        'luxury_reps' => 'integer',
        'economy_price_per_technique' => 'decimal:2',
        'economy_duration_minutes' => 'decimal:2',
        'economy_reps' => 'integer',
        'intensity_percent' => 'integer',
        'speed_percent' => 'integer',
        'is_active' => 'boolean',
    ];

    public function techniques(): HasMany
    {
        return $this->hasMany(MassageTechnique::class)->orderBy('order');
    }

    public function getPainLevelLabelAttribute(): string
    {
        return match ($this->pain_level) {
            'severe' => 'ألم شديد',
            'moderate' => 'ألم متوسط',
            default => $this->pain_level,
        };
    }

    public function getWeightBracketLabelAttribute(): string
    {
        return match ($this->weight_bracket) {
            '30_55' => '30 كجم : 55 كجم',
            '55_100' => '55 كجم : 100 كجم',
            '100_300' => '100 كجم : 300 كجم',
            default => $this->weight_bracket,
        };
    }

    public function getBloodTypeLabelAttribute(): string
    {
        return "فصيلة {$this->blood_type}";
    }
}
