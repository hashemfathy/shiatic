<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChiropracticRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_type',
        'region_number',
        'name',
        'diagram_numbers',
        'price_per_technique',
        'duration_seconds',
        'is_active',
    ];

    protected $casts = [
        'diagram_numbers' => 'array',
        'price_per_technique' => 'decimal:2',
        'duration_seconds' => 'integer',
        'is_active' => 'boolean',
        'region_number' => 'integer',
    ];

    public function techniques(): HasMany
    {
        return $this->hasMany(ChiropracticTechnique::class)->orderBy('order');
    }

    public function getPlanTypeLabelAttribute(): string
    {
        return $this->plan_type === 'economy' ? 'اقتصادي' : 'علاجي مكثف';
    }
}
