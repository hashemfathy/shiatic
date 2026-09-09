<?php

namespace App\Helpers;

class HijamaHelper
{
    /**
     * Map of cups per region for Intensive style
     */
    public static array $intensiveCupsMap = [
        1 => 3, 2 => 1, 3 => 2, 4 => 4, 5 => 3, 6 => 1, 7 => 2, 8 => 4, 9 => 2, 10 => 2,
        11 => 3, 12 => 3, 13 => 2, 14 => 2, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 3, 20 => 1,
        21 => 2, 22 => 3, 23 => 2, 24 => 2, 25 => 3, 26 => 2, 27 => 2, 28 => 3, 29 => 3, 30 => 3,
        31 => 2, 32 => 2, 33 => 1, 34 => 2, 35 => 1, 36 => 2, 37 => 2, 38 => 2, 39 => 2,
    ];

    /**
     * Map of cups per region for Economy style
     */
    public static array $economyCupsMap = [
        1 => 2, 2 => 1, 3 => 1, 4 => 2, 5 => 2, 6 => 1, 7 => 1, 8 => 2, 9 => 2, 10 => 1,
        11 => 1, 12 => 1, 13 => 1, 14 => 1, 15 => 1, 16 => 1, 17 => 1, 18 => 1, 19 => 1, 20 => 1,
        21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 1, 26 => 1, 27 => 1, 28 => 1, 29 => 1, 30 => 1,
        31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 1, 38 => 1, 39 => 1,
    ];

    /**
     * Get cup count for a specific body region based on style (intensive vs economy)
     */
    public static function getRegionCups(int $region, string $style = 'intensive'): int
    {
        if ($style === 'intensive') {
            return static::$intensiveCupsMap[$region] ?? 1;
        }

        return static::$economyCupsMap[$region] ?? 1;
    }

    /**
     * Get cup unit price based on total cup count tier
     */
    public static function getCupPrice(int $totalCups): int
    {
        if ($totalCups > 20) {
            return 35;
        } elseif ($totalCups >= 16) {
            return 37;
        } elseif ($totalCups >= 11) {
            return 40;
        }
        return 45;
    }

    /**
     * Calculate total cups, unit cup price, total price, and duration for selected regions and style
     * 
     * @param array $regions Selected region numbers (1-39)
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function calculate(array $regions, string $style = 'intensive'): array
    {
        $totalCups = 0;
        foreach ($regions as $region) {
            $totalCups += static::getRegionCups((int)$region, $style);
        }

        $cupPrice = static::getCupPrice($totalCups);
        $totalPrice = $totalCups * $cupPrice;
        $duration = $totalCups > 0 ? (10 + $totalCups) : 0;

        return [
            'total_cups'  => $totalCups,
            'cup_price'   => $cupPrice,
            'total_price' => $totalPrice,
            'duration'    => $duration,
        ];
    }
}
