<?php

namespace App\Helpers;

class RehabilitationHelper
{
    /**
     * Price per minute for Rehabilitation (التأهيل) in EGP
     */
    public static int $pricePerMinute = 12;

    /**
     * Calculate rehabilitation duration and total price based on pain counts and protocol style
     * 
     * @param int $severeCount Count of severe pain areas
     * @param int $moderateCount Count of moderate pain areas
     * @param int $mildCount Count of mild pain areas
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function calculate(int $severeCount, int $moderateCount, int $mildCount = 0, string $style = 'intensive'): array
    {
        $hasAnyPain = ($severeCount > 0 || $moderateCount > 0 || $mildCount > 0);
        if (!$hasAnyPain) {
            return [
                'duration' => 0,
                'price_per_minute' => static::$pricePerMinute,
                'total_price' => 0,
            ];
        }

        $duration = 0;
        if ($style === 'intensive') {
            if ($severeCount > 0 || $moderateCount > 0) {
                $duration = 10;
            } else {
                $duration = 5;
            }
        } else {
            // Economy style is always 5 minutes when any pain is present
            $duration = 5;
        }

        $totalPrice = $duration * static::$pricePerMinute;

        return [
            'duration' => $duration,
            'price_per_minute' => static::$pricePerMinute,
            'total_price' => $totalPrice,
        ];
    }
}
