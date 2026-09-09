<?php

namespace App\Helpers;

class TherapeuticMassageHelper
{
    /**
     * Massage technique description and style details per blood type
     */
    public static array $bloodTypeMatrix = [
        'A' => [
            'label' => 'فصيلة A',
            'base_technique' => 'مسحي علاجي واسترخائي عميق (وتري زلالي / انبساطي تجمعي)',
            'pressure' => 'متوسط إلى خفيف',
        ],
        'B' => [
            'label' => 'فصيلة B',
            'base_technique' => 'ضغط نقطي وعضلي علاجي (ليمفاوي عصبي / وتري زلالي)',
            'pressure' => 'متوسط إلى شديد',
        ],
        'AB' => [
            'label' => 'فصيلة AB',
            'base_technique' => 'تقويم عضلي ومسحي مركب (عصبي زلالي / عقدي حمضي)',
            'pressure' => 'متنوع ومركب',
        ],
        'O' => [
            'label' => 'فصيلة O',
            'base_technique' => 'علاجي هارد عميق وتفكيك التصلبات (وتري مفصلي / حمضي عصبي)',
            'pressure' => 'عميق وقوي (هارد)',
        ],
    ];

    /**
     * Map of techniques count per region for Severe Pain per blood type and weight bracket
     */
    public static array $severeTechniqueMap = [
        'A' => [
            '30_55' => [
                1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 3, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '55_100' => [
                1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 3, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '100_300' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
        ],
        'AB' => [
            '30_55' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '55_100' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '100_300' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
        ],
        'B' => [
            '30_55' => [
                1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 3, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '55_100' => [
                1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 3, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '100_300' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
        ],
        'O' => [
            '30_55' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '55_100' => [
                1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 3, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
            '100_300' => [
                1 => 3, 2 => 2, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 2,
                11 => 6, 12 => 6, 13 => 5, 14 => 5, 15 => 2, 16 => 2, 17 => 1, 18 => 2, 19 => 1, 20 => 1,
                21 => 2, 22 => 1, 23 => 1, 24 => 1, 25 => 5, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
                31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 2, 39 => 2,
            ],
        ],
    ];

    /**
     * Map of techniques count per region for Moderate Pain
     */
    public static array $moderateTechniqueMap = [
        1 => 3, 2 => 1, 3 => 2, 4 => 3, 5 => 3, 6 => 1, 7 => 2, 8 => 3, 9 => 1, 10 => 1,
        11 => 3, 12 => 3, 13 => 2, 14 => 2, 15 => 1, 16 => 1, 17 => 1, 18 => 1, 19 => 1, 20 => 1,
        21 => 1, 22 => 1, 23 => 1, 24 => 1, 25 => 4, 26 => 1, 27 => 2, 28 => 4, 29 => 1, 30 => 2,
        31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1, 37 => 2, 38 => 1, 39 => 1,
    ];

    /**
     * Determine weight bracket key based on weight in kg
     */
    public static function getWeightBracket(float $weight): string
    {
        if ($weight < 55) {
            return '30_55';
        } elseif ($weight < 100) {
            return '55_100';
        }
        return '100_300';
    }

    /**
     * Calculate therapeutic massage duration, pricing, and technique details
     * based on Blood Type, Weight, and Severe & Moderate Pain Severities.
     * 
     * @param string $bloodType Blood Type ('A', 'B', 'AB', 'O')
     * @param float $weight Weight in kg
     * @param array $severeRegions Selected severe pain region numbers
     * @param array $moderateRegions Selected moderate pain region numbers
     * @param string $style 'intensive' or 'economy'
     * @return array
     */
    public static function calculate(
        string $bloodType,
        float $weight,
        array $severeRegions = [],
        array $moderateRegions = [],
        string $style = 'intensive'
    ): array {
        // Fallback 'dont_know' -> 'O'
        if (empty($bloodType) || $bloodType === 'dont_know') {
            $bloodType = 'O';
        }
        $bloodTypeKey = strtoupper($bloodType);
        if (!isset(static::$bloodTypeMatrix[$bloodTypeKey])) {
            $bloodTypeKey = 'O';
        }

        $bracket = static::getWeightBracket($weight);
        $bloodMap = static::$severeTechniqueMap[$bloodTypeKey] ?? static::$severeTechniqueMap['O'];
        $sevMap = $bloodMap[$bracket] ?? $bloodMap['55_100'];

        $severeTechniqueCount = 0;
        foreach ($severeRegions as $rNum) {
            $severeTechniqueCount += $sevMap[(int)$rNum] ?? 1;
        }

        $moderateTechniqueCount = 0;
        foreach ($moderateRegions as $rNum) {
            $moderateTechniqueCount += static::$moderateTechniqueMap[(int)$rNum] ?? 1;
        }

        $totalTechniques = $severeTechniqueCount + $moderateTechniqueCount;

        if ($totalTechniques === 0) {
            return [
                'blood_type' => $bloodTypeKey,
                'weight' => $weight,
                'duration' => 0,
                'total_price' => 0,
                'technique' => 'لم يتم اختيار مناطق ألم',
            ];
        }

        $bracket = static::getWeightBracket($weight);

        // Parameters per region based on bracket & severity (Severe & Moderate)
        $severeParams = [
            '30_55'   => ['intensive' => ['duration' => 1.5, 'price' => 21], 'economy' => ['duration' => 1.0, 'price' => 12]],
            '55_100'  => ['intensive' => ['duration' => 2.0, 'price' => 32], 'economy' => ['duration' => 1.5, 'price' => 21]],
            '100_300' => ['intensive' => ['duration' => 2.5, 'price' => 45], 'economy' => ['duration' => 2.0, 'price' => 24]],
        ];

        $moderateParams = [
            '30_55'   => ['intensive' => ['duration' => 1.5, 'price' => 21], 'economy' => ['duration' => 1.0, 'price' => 12]],
            '55_100'  => ['intensive' => ['duration' => 2.0, 'price' => 32], 'economy' => ['duration' => 1.5, 'price' => 21]],
            '100_300' => ['intensive' => ['duration' => 2.5, 'price' => 45], 'economy' => ['duration' => 2.0, 'price' => 24]],
        ];

        $sev = $severeParams[$bracket][$style];
        $mod = $moderateParams[$bracket][$style];

        $totalDuration = ($severeTechniqueCount * $sev['duration']) + ($moderateTechniqueCount * $mod['duration']);
        $totalPrice = ($severeTechniqueCount * $sev['price']) + ($moderateTechniqueCount * $mod['price']);

        $matrixInfo = static::$bloodTypeMatrix[$bloodTypeKey];

        return [
            'blood_type'             => $bloodTypeKey,
            'blood_type_label'       => $matrixInfo['label'],
            'weight'                 => $weight,
            'weight_bracket'         => $bracket,
            'duration'               => round($totalDuration, 1),
            'total_price'            => round($totalPrice, 2),
            'technique'              => $matrixInfo['base_technique'],
            'pressure'               => $matrixInfo['pressure'],
            'severe_count'           => count($severeRegions),
            'moderate_count'         => count($moderateRegions),
            'severe_techniques'      => $severeTechniqueCount,
            'moderate_techniques'    => $moderateTechniqueCount,
            'total_techniques'       => $totalTechniques,
        ];
    }

    /**
     * Render HTML card and table for therapeutic details in Filament Admin
     */
    public static function renderTherapeuticDetails($record)
    {
        $desc = $record->description ?? $record->complaint ?? '';
        if (empty($desc)) {
            return new \Illuminate\Support\HtmlString('لا توجد تفاصيل متاحة للسيشن العلاجية.');
        }

        $parts = explode(' | ', $desc);
        $cardsHtml = '';
        foreach ($parts as $part) {
            $cardsHtml .= '<div style="margin-bottom: 0.6rem; padding: 0.8rem 1rem; background: #262626; border-right: 4px solid #ff9d42; border-radius: 8px; color: #fff; font-size: 0.95rem;">' . e($part) . '</div>';
        }

        $regionsHtml = '';
        $regions = [];
        if ($record instanceof \App\Models\Request && $record->relationLoaded('regions')) {
            $regions = $record->regions;
        } elseif ($record instanceof \App\Models\Request) {
            $regions = $record->regions()->get();
        }

        if (!empty($regions) && count($regions) > 0) {
            $regionRows = '';
            foreach ($regions as $index => $r) {
                $num = $r->region_number;
                $reps = $r->repetitions;
                $regionRows .= "<tr style='border-bottom: 1px solid #3f3f46;'>
                    <td style='padding: 8px; border: 1px solid #3f3f46;'>" . ($index + 1) . "</td>
                    <td style='padding: 8px; border: 1px solid #3f3f46; font-weight: bold; color: #ff9d42;'>المنطقة {$num}</td>
                    <td style='padding: 8px; border: 1px solid #3f3f46; font-weight: bold; color: #34d399;'>{$reps} تكنيك</td>
                </tr>";
            }

            $regionsHtml = "
            <div style='margin-top: 15px;'>
                <h5 style='color: #ff9d42; margin-bottom: 10px; font-weight: bold;'>📍 مناطق الألم المحددة وتكنيكاتها:</h5>
                <div style='overflow-x: auto;'>
                    <table style='width: 100%; border-collapse: collapse; text-align: center; font-size: 0.9rem; background: #1e1e24; color: #fff; border: 1px solid #3f3f46;'>
                        <thead>
                            <tr style='background: #27272a; color: #ff9d42;'>
                                <th style='padding: 8px; border: 1px solid #3f3f46;'>م</th>
                                <th style='padding: 8px; border: 1px solid #3f3f46;'>رقم المنطقة</th>
                                <th style='padding: 8px; border: 1px solid #3f3f46;'>عدد التكنيكات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$regionRows}
                        </tbody>
                    </table>
                </div>
            </div>";
        }

        return new \Illuminate\Support\HtmlString("
            <div style='direction: rtl; text-align: right; line-height: 1.6; font-family: sans-serif; background: #18181b; padding: 18px; border-radius: 10px; border: 1px solid #3f3f46;'>
                <h4 style='color: #ff9d42; margin-bottom: 15px; font-weight: bold;'>🩺 تفاصيل السيشن العلاجية والتكنيكات المعتمدة:</h4>
                {$cardsHtml}
                {$regionsHtml}
            </div>
        ");
    }

    public static function parseTherapeuticDescription($desc)
    {
        $protocol = 'intensive';
        if (str_contains($desc, 'اقتصادي')) {
            $protocol = 'economy';
        }

        $bloodType = 'A';
        if (preg_match('/فصيلة الدم\s*\(([A-Za-z]+)\)/u', $desc, $m)) {
            $bloodType = strtoupper($m[1]);
        }

        $weight = 75;
        if (preg_match('/الوزن\s*\(([0-9.]+)\s*كجم\)/u', $desc, $m)) {
            $weight = (float)$m[1];
        }

        $severeRegions = [];
        if (preg_match('/شديد\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $severeRegions = array_filter(array_map('intval', explode(',', $str)));
            }
        }

        $moderateRegions = [];
        if (preg_match('/متوسط\s*\[([^\]]+)\]/u', $desc, $m)) {
            $str = trim($m[1]);
            if ($str !== 'لا يوجد') {
                $moderateRegions = array_filter(array_map('intval', explode(',', $str)));
            }
        }

        return [
            'protocol' => $protocol,
            'blood_type' => $bloodType,
            'weight' => $weight,
            'severe_regions' => $severeRegions,
            'moderate_regions' => $moderateRegions,
        ];
    }

    public static function renderDetailedTechniquesTable($record)
    {
        $desc = $record->description ?? $record->complaint ?? '';
        if (empty($desc)) {
            return new \Illuminate\Support\HtmlString('لا توجد تفاصيل حجز لعرض التكنيكات.');
        }

        $parsed = self::parseTherapeuticDescription($desc);
        $bloodType = in_array($parsed['blood_type'], ['A', 'B', 'AB', 'O']) ? $parsed['blood_type'] : 'A';
        $weight = $parsed['weight'];
        $bracket = self::getWeightBracket($weight);
        $protocol = $parsed['protocol'];
        $severeRegions = $parsed['severe_regions'];
        $moderateRegions = $parsed['moderate_regions'];

        $jsonPath = storage_path('app/therapeutic_techniques.json');
        if (!file_exists($jsonPath)) {
            return new \Illuminate\Support\HtmlString('ملف التكنيكات غير متوفر.');
        }

        $db = json_decode(file_get_contents($jsonPath), true);
        $typeData = $db[$bloodType] ?? [];

        $severeRows = [];
        if (!empty($severeRegions) && isset($typeData['severe'][$bracket])) {
            foreach ($typeData['severe'][$bracket] as $row) {
                if (in_array((int)$row['region'], $severeRegions)) {
                    $severeRows[] = $row;
                }
            }
        }

        $moderateRows = [];
        if (!empty($moderateRegions) && isset($typeData['moderate'][$bracket])) {
            foreach ($typeData['moderate'][$bracket] as $row) {
                if (in_array((int)$row['region'], $moderateRegions)) {
                    $moderateRows[] = $row;
                }
            }
        }

        if (empty($severeRows) && empty($moderateRows)) {
            return new \Illuminate\Support\HtmlString('لا توجد تكنيكات مسجلة لمناطق الألم المحددة.');
        }

        $rowsHtml = '';
        $counter = 1;

        foreach ($severeRows as $r) {
            $repVal = $r['reps'];
            if (str_contains($repVal, '/')) {
                $parts = explode('/', $repVal);
                $repVal = ($protocol === 'intensive') ? trim($parts[0]) : trim($parts[1]);
            }
            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(239, 68, 68, 0.05);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;'>🔴 شديد</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$r['region']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$r['name']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$r['technique']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r['tool']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r['direction']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$repVal}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #f59e0b;'>{$r['intensity']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #a855f7;'>{$r['speed']}</td>
            </tr>";
            $counter++;
        }

        foreach ($moderateRows as $r) {
            $repVal = $r['reps'];
            if (str_contains($repVal, '/')) {
                $parts = explode('/', $repVal);
                $repVal = ($protocol === 'intensive') ? trim($parts[0]) : trim($parts[1]);
            }
            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(245, 158, 11, 0.05);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #f59e0b; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;'>🟠 متوسط</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$r['region']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$r['name']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$r['technique']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r['tool']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r['direction']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$repVal}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #f59e0b;'>{$r['intensity']}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #a855f7;'>{$r['speed']}</td>
            </tr>";
            $counter++;
        }

        $protoLabel = ($protocol === 'intensive') ? 'بروتوكول مكثف' : 'بروتوكول اقتصادي';
        $weightLabel = match($bracket) {
            '30_55' => '30 إلى 55 كجم',
            '55_100' => '55 إلى 100 كجم',
            '100_300' => '100 إلى 300 كجم',
            default => '55 إلى 100 كجم'
        };

        return new \Illuminate\Support\HtmlString("
        <div style='direction: rtl; text-align: right; font-family: sans-serif; margin-top: 15px;'>
            <div style='background: #0f172a; padding: 14px 18px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;'>
                <div>
                    <span style='color: #ff9d42; font-weight: bold; font-size: 1.05rem;'>📋 تكنيكات المساج العلاجي المعتمدة من ملف فصيلة الدم:</span>
                </div>
                <div style='display: flex; gap: 8px; flex-wrap: wrap;'>
                    <span style='background: #1e293b; color: #38bdf8; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>🩸 فصيلة {$bloodType}</span>
                    <span style='background: #1e293b; color: #a855f7; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>⚖️ فئة الوزن ({$weightLabel})</span>
                    <span style='background: #1e293b; color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;'>🎯 {$protoLabel}</span>
                </div>
            </div>
            <div style='overflow-x: auto;'>
                <table style='width: 100%; border-collapse: collapse; text-align: center; font-size: 0.85rem; background: #0f172a; color: #f8fafc; border: 1px solid #334155; border-radius: 8px;'>
                    <thead>
                        <tr style='background: #1e293b; color: #ff9d42; border-bottom: 2px solid #334155;'>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>م</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>مستوى الألم</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>رقم المنطقة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>المنطقة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>نوع المساج</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الأداة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الاتجاه</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>التكرار</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>الشدة</th>
                            <th style='padding: 10px 8px; border: 1px solid #334155;'>السرعة</th>
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

    public static function renderTherapeuticBodyMaps($record)
    {
        $desc = $record->description ?? $record->complaint ?? '';
        $parsed = self::parseTherapeuticDescription($desc);
        $severeRegions = $parsed['severe_regions'];
        $moderateRegions = $parsed['moderate_regions'];

        $regionCoords = [
            1 => ['top' => 59, 'left' => 82.8], 2 => ['top' => 69.8, 'left' => 82.2], 3 => ['top' => 77.5, 'left' => 82.2],
            4 => ['top' => 90.5, 'left' => 82.2], 5 => ['top' => 59.5, 'left' => 88.2], 6 => ['top' => 70.5, 'left' => 88.2],
            7 => ['top' => 78.5, 'left' => 89.2], 8 => ['top' => 91.5, 'left' => 88.2], 9 => ['top' => 45.5, 'left' => 82.2],
            10 => ['top' => 45.5, 'left' => 88.2], 11 => ['top' => 36.5, 'left' => 82.2], 12 => ['top' => 37.5, 'left' => 89.2],
            13 => ['top' => 25.5, 'left' => 83.2], 14 => ['top' => 26.5, 'left' => 89.2], 15 => ['top' => 17.5, 'left' => 83.2],
            16 => ['top' => 17.5, 'left' => 88.5], 17 => ['top' => 20.5, 'left' => 77.8], 18 => ['top' => 28.5, 'left' => 77],
            19 => ['top' => 38.5, 'left' => 76.5], 20 => ['top' => 20.5, 'left' => 93.8], 21 => ['top' => 29.5, 'left' => 94.6],
            22 => ['top' => 39.5, 'left' => 95.2], 23 => ['top' => 20.5, 'left' => 64], 24 => ['top' => 18.5, 'left' => 45.2],
            25 => ['top' => 54.5, 'left' => 10], 26 => ['top' => 69.5, 'left' => 10], 27 => ['top' => 78.5, 'left' => 10],
            28 => ['top' => 55, 'left' => 17.2], 29 => ['top' => 69.5, 'left' => 17.2], 30 => ['top' => 79.5, 'left' => 17.2],
            31 => ['top' => 23.5, 'left' => 15.5], 32 => ['top' => 23.5, 'left' => 10.5], 33 => ['top' => 19.5, 'left' => 20],
            34 => ['top' => 26.5, 'left' => 21.5], 35 => ['top' => 19.5, 'left' => 6], 36 => ['top' => 27.5, 'left' => 5.5],
            37 => ['top' => 9.5, 'left' => 85.8], 38 => ['top' => 89.5, 'left' => 16.2], 39 => ['top' => 88.5, 'left' => 10]
        ];

        // Severe hotspots
        $severeHotspots = '';
        foreach ($regionCoords as $num => $coord) {
            $isSelected = in_array($num, $severeRegions);
            $bg = $isSelected ? 'background: #ef4444; border-color: #fca5a5; color: #fff; font-weight: bold; box-shadow: 0 0 10px #ef4444;' : 'background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #94a3b8;';
            $severeHotspots .= "<div style='position: absolute; top: {$coord['top']}%; left: {$coord['left']}%; transform: translate(-50%, -50%); width: 22px; height: 22px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; font-size: 11px; {$bg}'>{$num}</div>";
        }

        // Moderate hotspots
        $moderateHotspots = '';
        foreach ($regionCoords as $num => $coord) {
            $isSelected = in_array($num, $moderateRegions);
            $bg = $isSelected ? 'background: #f59e0b; border-color: #fde68a; color: #fff; font-weight: bold; box-shadow: 0 0 10px #f59e0b;' : 'background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); color: #94a3b8;';
            $moderateHotspots .= "<div style='position: absolute; top: {$coord['top']}%; left: {$coord['left']}%; transform: translate(-50%, -50%); width: 22px; height: 22px; border-radius: 50%; border: 2px solid; display: flex; align-items: center; justify-content: center; font-size: 11px; {$bg}'>{$num}</div>";
        }

        $severeCount = count($severeRegions);
        $moderateCount = count($moderateRegions);

        return new \Illuminate\Support\HtmlString("
        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-top: 15px; direction: rtl;'>
            <div style='background: #0f172a; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 15px; text-align: center;'>
                <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #334155; padding-bottom: 8px;'>
                    <span style='color: #ef4444; font-weight: bold; font-size: 1rem;'>🔴 خريطة مناطق الألم الشديد</span>
                    <span style='background: #ef4444; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;'>{$severeCount} منطقة</span>
                </div>
                <div style='position: relative; display: inline-block; max-width: 500px; width: 100%; aspect-ratio: 438 / 166.32;'>
                    <img src='/images/body.jpg' alt='Severe Pain Chart' style='width: 100%; height: auto; border-radius: 8px; border: 1px solid #334155;' />
                    {$severeHotspots}
                </div>
            </div>

            <div style='background: #0f172a; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 15px; text-align: center;'>
                <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #334155; padding-bottom: 8px;'>
                    <span style='color: #f59e0b; font-weight: bold; font-size: 1rem;'>🟠 خريطة مناطق الألم المتوسط</span>
                    <span style='background: #f59e0b; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;'>{$moderateCount} منطقة</span>
                </div>
                <div style='position: relative; display: inline-block; max-width: 500px; width: 100%; aspect-ratio: 438 / 166.32;'>
                    <img src='/images/body.jpg' alt='Moderate Pain Chart' style='width: 100%; height: auto; border-radius: 8px; border: 1px solid #334155;' />
                    {$moderateHotspots}
                </div>
            </div>
        </div>
        ");
    }
}
