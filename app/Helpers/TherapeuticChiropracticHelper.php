<?php

namespace App\Helpers;

use App\Models\ChiropracticRegion;
use App\Models\ChiropracticTechnique;
use App\Models\Request as BookingRequest;
use App\Models\Visit;
use Illuminate\Support\Facades\Cache;

class TherapeuticChiropracticHelper
{
    /**
     * Default Price per chiropractic technique in EGP
     */
    public static float $pricePerTechnique = 13.0;

    /**
     * Default Duration per chiropractic technique in minutes (15 seconds = 0.25 min)
     */
    public static float $durationPerTechnique = 0.25;

    /**
     * Fallback Map of 5 Main Body Region Groups with assigned region numbers (1 to 39)
     */
    public static array $defaultRegionGroups = [
        1 => [
            'name' => 'منطقة 1 العنقية',
            'regions' => [15, 16, 37]
        ],
        2 => [
            'name' => 'منطقة 2 الأكتاف والذراعين',
            'regions' => [17, 18, 19, 20, 21, 22, 23, 24, 33, 34, 35, 36]
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
            'regions' => [1, 2, 3, 4, 5, 6, 7, 8, 25, 26, 27, 28, 29, 30, 31, 32, 38, 39]
        ]
    ];

    /**
     * Get active regions from database or cache
     */
    public static function getActiveRegions(): array
    {
        try {
            $regions = ChiropracticRegion::with(['techniques' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])->where('is_active', true)->get();

            if ($regions->isNotEmpty()) {
                return $regions->toArray();
            }
        } catch (\Throwable $e) {
            // In case tables do not exist yet
        }

        return [];
    }

    /**
     * Get region groups configuration dynamically
     */
    public static function getRegionGroups(): array
    {
        $activeRegions = static::getActiveRegions();
        if (empty($activeRegions)) {
            return static::$defaultRegionGroups;
        }

        $groups = [];
        foreach ($activeRegions as $reg) {
            $gNum = (int)$reg['region_number'];
            if (!isset($groups[$gNum])) {
                $diagramNums = is_array($reg['diagram_numbers']) 
                    ? array_map('intval', $reg['diagram_numbers']) 
                    : (static::$defaultRegionGroups[$gNum]['regions'] ?? []);

                $groups[$gNum] = [
                    'name' => $reg['name'],
                    'regions' => $diagramNums,
                ];
            }
        }

        return !empty($groups) ? $groups : static::$defaultRegionGroups;
    }

    /**
     * Get the region group ID (1 to 5) for a specific region number
     */
    public static function getGroupForRegion(int $regionNumber): int
    {
        $groups = static::getRegionGroups();
        foreach ($groups as $groupId => $groupInfo) {
            if (in_array((int)$regionNumber, $groupInfo['regions'], true)) {
                return $groupId;
            }
        }
        return 5;
    }

    /**
     * Get all techniques for given group IDs based on style ('intensive' or 'economy')
     */
    public static function getTechniquesForGroups(array $groupIds, string $style = 'intensive'): array
    {
        $styleKey = ($style === 'economy') ? 'economy' : 'intensive';
        $activeRegions = static::getActiveRegions();

        if (!empty($activeRegions)) {
            $filtered = [];
            foreach ($activeRegions as $reg) {
                if ($reg['plan_type'] === $styleKey && in_array((int)$reg['region_number'], $groupIds, true)) {
                    foreach ($reg['techniques'] as $tech) {
                        $filtered[] = [
                            'group' => (int)$reg['region_number'],
                            'group_name' => $reg['name'],
                            'region' => $tech['target_region_code'],
                            'name' => $tech['name'],
                            'position' => $tech['position'],
                            'direction' => $tech['direction'],
                            'rep' => (int)$tech['rep'],
                            'order' => (int)$tech['order'],
                            'price' => (float)$reg['price_per_technique'],
                            'duration_seconds' => (int)$reg['duration_seconds'],
                        ];
                    }
                }
            }

            if (!empty($filtered)) {
                usort($filtered, fn($a, $b) => ($a['group'] <=> $b['group']) ?: ($a['order'] <=> $b['order']));
                return $filtered;
            }
        }

        return [];
    }

    /**
     * Calculate total techniques, duration, price and active groups for selected regions
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
        $rawTotalPrice = 0.0;
        $totalDurationMinutes = 0.0;
        $activeGroupNames = [];

        $allTechniques = static::getTechniquesForGroups($activeGroupIds, $styleKey);
        $groupsConfig = static::getRegionGroups();

        if (!empty($allTechniques)) {
            foreach ($allTechniques as $tech) {
                $totalTechniques++;
                $rawTotalPrice += ($tech['price'] ?? 13.0);
                $totalDurationMinutes += (($tech['duration_seconds'] ?? 15) / 60.0);
            }

            foreach ($activeGroupIds as $gId) {
                $activeGroupNames[] = $groupsConfig[$gId]['name'] ?? "منطقة {$gId}";
            }
        } else {
            // Fallback default calculation if database empty
            $totalTechniques = count($activeGroupIds) * 10;
            $rawTotalPrice = count($activeGroupIds) * 10 * 13.0;
            $totalDurationMinutes = $totalTechniques * 0.25;
            foreach ($activeGroupIds as $gId) {
                $activeGroupNames[] = static::$defaultRegionGroups[$gId]['name'] ?? "منطقة {$gId}";
            }
        }

        $duration = round($totalDurationMinutes, 2);

        // Apply 15% discount on chiropractic when selecting more than 3 regions (groups)
        $discountAmount = 0.0;
        if (count($activeGroupIds) > 3) {
            $discountAmount = round($rawTotalPrice * 0.15, 2);
        }
        $totalPrice = round($rawTotalPrice - $discountAmount, 2);

        return [
            'active_groups' => $activeGroupIds,
            'active_group_names' => $activeGroupNames,
            'total_techniques' => $totalTechniques,
            'duration_per_technique' => static::$durationPerTechnique,
            'raw_total_price' => $rawTotalPrice,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'duration' => $duration,
            'selected_regions_count' => count($uniqueRegions),
        ];
    }

    /**
     * Render HTML table of chiropractic techniques for therapeutic booking/visit in Filament Admin
     */
    public static function renderChiropracticTechniquesTable($record)
    {
        $request = null;
        if ($record instanceof Visit) {
            if ($record->request_id) {
                $request = BookingRequest::find($record->request_id);
            }
            if (!$request && $record->client) {
                $request = BookingRequest::where('phone', $record->client->phone)
                    ->where('date', $record->date)
                    ->first();
            }
        } elseif ($record instanceof BookingRequest) {
            $request = $record;
        }

        $desc = $request?->description ?? $record->description ?? $record->complaint ?? '';
        if (empty($desc)) {
            return new \Illuminate\Support\HtmlString('');
        }

        // Determine protocol style
        $style = 'intensive';
        if (str_contains($desc, 'اقتصادي')) {
            $style = 'economy';
        }

        // Extract pain regions
        $allRegions = [];
        if (preg_match('/شديد\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $allRegions = array_merge($allRegions, array_filter(array_map('intval', explode(',', $str))));
            }
        }
        if (preg_match('/متوسط\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $allRegions = array_merge($allRegions, array_filter(array_map('intval', explode(',', $str))));
            }
        }

        $allRegions = array_unique($allRegions);
        if (empty($allRegions)) {
            return new \Illuminate\Support\HtmlString('');
        }

        $calc = static::calculate($allRegions, $style);
        $activeGroups = $calc['active_groups'];
        $techniques = static::getTechniquesForGroups($activeGroups, $style);

        if (empty($techniques)) {
            return new \Illuminate\Support\HtmlString('');
        }

        $rowsHtml = '';
        foreach ($techniques as $index => $tech) {
            $counter = $index + 1;
            $groupName = $tech['group_name'] ?? (static::getRegionGroups()[$tech['group']]['name'] ?? "المجموعة {$tech['group']}");

            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(56, 189, 248, 0.03);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #0284c7; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;'>{$groupName}</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$tech['region']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$tech['name']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$tech['position']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$tech['direction']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$tech['rep']}</td>
            </tr>";
        }

        $styleLabel = ($style === 'intensive') ? 'البروتوكول المكثف' : 'البروتوكول الاقتصادي';
        $totalCount = count($techniques);
        $totalMinutes = $calc['duration'];
        $totalPrice = number_format($calc['total_price'], 2);

        return new \Illuminate\Support\HtmlString("
        <div style='direction: rtl; text-align: right; font-family: sans-serif; margin-top: 20px;'>
            <div style='background: #0f172a; padding: 14px 18px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;'>
                <div>
                    <span style='color: #38bdf8; font-weight: bold; font-size: 1.05rem;'>🦴 تكنيكات الكيروبراكتيك العلاجي المعتمدة ({$styleLabel}):</span>
                </div>
                <div style='display: flex; gap: 8px; flex-wrap: wrap;'>
                    <span style='background: #1e293b; color: #ff9d42; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>🔢 {$totalCount} تكنيك</span>
                    <span style='background: #1e293b; color: #34d399; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>⏱️ {$totalMinutes} دقيقة</span>
                    <span style='background: #1e293b; color: #fbbf24; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>💰 {$totalPrice} ج.م</span>
                </div>
            </div>
            <div style='overflow-x: auto;'>
                <table style='width: 100%; border-collapse: collapse; text-align: center; font-size: 0.85rem; background: #0f172a; color: #f8fafc; border: 1px solid #334155; border-radius: 8px;'>
                    <thead>
                        <tr style='background: #1e293b; color: #38bdf8; border-bottom: 2px solid #334155;'>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>م</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>المجموعة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>رقم المنطقة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>اسم التكنيك</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الوضعية</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الاتجاه</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>مللي</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rowsHtml}
                    </tbody>
                </table>
            </div>
        </div>
        ");
    }
}
