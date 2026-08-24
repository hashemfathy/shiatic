<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'expires_at',
        'max_uses',
        'uses',
        'min_booking_value',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active' => 'boolean',
        'value' => 'float',
        'min_booking_value' => 'float',
        'uses' => 'integer',
        'max_uses' => 'integer',
    ];

    /**
     * Check if coupon is valid for a given date and total price.
     */
    public function isValidFor(?string $date = null, float $bookingValue = 0): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check expiration
        if ($this->expires_at) {
            $checkDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::today();
            if ($checkDate->greaterThan($this->expires_at)) {
                return false;
            }
        }

        // Check max uses
        if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
            return false;
        }

        // Check minimum booking value
        if ($bookingValue < $this->min_booking_value) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscountFor(float $bookingValue): float
    {
        if ($this->type === 'percentage') {
            return round($bookingValue * ($this->value / 100), 2);
        }

        // Fixed discount, but cannot exceed the booking value
        return min($this->value, $bookingValue);
    }
}
