<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiropracticTechnique extends Model
{
    use HasFactory;

    protected $fillable = [
        'chiropractic_region_id',
        'order',
        'target_region_code',
        'name',
        'position',
        'direction',
        'rep',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'rep' => 'integer',
        'is_active' => 'boolean',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(ChiropracticRegion::class, 'chiropractic_region_id');
    }
}
