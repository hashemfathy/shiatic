<?php

namespace App\Helpers;

class TherapeuticChiropracticHelper
{
    /**
     * Price per chiropractic technique in EGP
     */
    public static float $pricePerTechnique = 16.23;

    /**
     * Duration per chiropractic technique in minutes (15 seconds = 0.25 min)
     */
    public static float $durationPerTechnique = 0.25;

    /**
     * Map of 5 Main Body Region Groups with assigned region numbers (1 to 39)
     */
    public static array $regionGroups = [
        1 => [
            'name' => 'منطقة 1 العنقية',
            'regions' => [15, 16, 37]
        ],
        2 => [
            'name' => 'منطقة 2 الأكتاف والذراعين',
            'regions' => [17, 18, 19, 20, 21, 22, 23, 24, 33, 35]
        ],
        3 => [
            'name' => 'منطقة 3 الصدرية',
            'regions' => [13, 14]
        ],
        4 => [
            'name' => 'منطقة 4 القطنية',
            'regions' => [9, 10, 11, 12]
        ],
        5 => [
            'name' => 'منطقة 5 القدمين والطرف السفلي',
            'regions' => [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30, 31, 32, 34, 36, 38, 39]
        ]
    ];

    /**
     * Total techniques count per region group based on style (intensive vs economy)
     */
    public static array $groupTechniquesMap = [
        'intensive' => [
            1 => 13, // العنقية (13 تكنيك)
            2 => 17, // الأكتاف والذراعين (17 تكنيك)
            3 => 13, // الصدرية (13 تكنيك)
            4 => 14, // القطنية (14 تكنيك)
            5 => 20  // القدمين والطرف السفلي (20 تكنيك)
        ],
        'economy' => [
            1 => 8,  // العنقية (8 تكنيكات)
            2 => 13, // الأكتاف والذراعين (13 تكنيك)
            3 => 9,  // الصدرية (9 تكنيكات)
            4 => 10, // القطنية (10 تكنيكات)
            5 => 14  // القدمين والطرف السفلي (14 تكنيك)
        ]
    ];

    /**
     * Get the region group ID (1 to 5) for a specific region number
     */
    public static function getGroupForRegion(int $regionNumber): int
    {
        foreach (static::$regionGroups as $groupId => $groupInfo) {
            if (in_array((int)$regionNumber, $groupInfo['regions'], true)) {
                return $groupId;
            }
        }
        return 5;
    }

    /**
     * Calculate total techniques, duration, price and active groups for selected regions
     * 
     * @param array $regions Selected region numbers (1-39)
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function calculate(array $regions, string $style = 'intensive'): array
    {
        $uniqueRegions = array_unique(array_map('intval', $regions));
        $activeGroupIds = [];

        foreach ($uniqueRegions as $rNum) {
            $groupId = static::getGroupForRegion($rNum);
            if (!in_array($groupId, $activeGroupIds, true)) {
                $activeGroupIds[] = $groupId;
            }
        }

        sort($activeGroupIds);

        $styleKey = ($style === 'economy') ? 'economy' : 'intensive';
        $totalTechniques = 0;
        $activeGroupNames = [];

        foreach ($activeGroupIds as $gId) {
            $totalTechniques += static::$groupTechniquesMap[$styleKey][$gId] ?? 0;
            $activeGroupNames[] = static::$regionGroups[$gId]['name'];
        }

        $duration = round($totalTechniques * static::$durationPerTechnique, 2);
        $totalPrice = round($totalTechniques * static::$pricePerTechnique, 2);

        return [
            'active_groups' => $activeGroupIds,
            'active_group_names' => $activeGroupNames,
            'total_techniques' => $totalTechniques,
            'price_per_technique' => static::$pricePerTechnique,
            'duration_per_technique' => static::$durationPerTechnique,
            'total_price' => $totalPrice,
            'duration' => $duration,
            'selected_regions_count' => count($uniqueRegions),
        ];
    }
}
