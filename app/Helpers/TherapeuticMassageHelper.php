<?php

namespace App\Helpers;

use App\Models\MassageProtocol;
use App\Models\MassageTechnique;

class TherapeuticMassageHelper
{
    /**
     * Massage technique description and style details per blood type
     */
    public static array $bloodTypeMatrix = [
        'A' => [
            'label' => 'فصيلة A',
            'base_technique' => 'مسحي وتري وعضلي (وتري زلالي / انبساطي حمضي وتجمعي)',
            'pressure' => 'شدة 60% وسرعة 20% (قبضة غ / إبهام)',
        ],
        'B' => [
            'label' => 'فصيلة B',
            'base_technique' => 'ضغط نقطي وعصبي (ليمفاوي عصبي / انبساطي تجمعي)',
            'pressure' => 'شدة 40% وسرعة 20% (إبهام / قبضة م)',
        ],
        'AB' => [
            'label' => 'فصيلة AB',
            'base_technique' => 'تقويم عضلي ومسحي مركب (عصبي زلالي / انبساطي مركب)',
            'pressure' => 'شدة 50% وسرعة 20% (إبهام / قبضة غ)',
        ],
        'O' => [
            'label' => 'فصيلة O',
            'base_technique' => 'علاجي عميق وتفكيك التصلبات (انبساطي حمضي / عصبي عميق ومفصلي)',
            'pressure' => 'شدة 30% وسرعة 20% (إبهام / كلوة)',
        ],
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
     * Get active MassageProtocol from database
     */
    public static function getProtocol(string $bloodType, string $painLevel, string $weightBracket): ?MassageProtocol
    {
        try {
            $bloodTypeKey = in_array(strtoupper($bloodType), ['A', 'B', 'AB', 'O']) ? strtoupper($bloodType) : 'O';
            return MassageProtocol::with(['techniques' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->where('blood_type', $bloodTypeKey)
            ->where('pain_level', $painLevel)
            ->where('weight_bracket', $weightBracket)
            ->where('is_active', true)
            ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Calculate therapeutic massage duration, pricing, and technique details
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
        $bloodTypeKey = in_array(strtoupper($bloodType), ['A', 'B', 'AB', 'O']) ? strtoupper($bloodType) : 'O';
        $styleKey = in_array($style, ['intensive', 'economy']) ? $style : 'intensive';
        $bracket = static::getWeightBracket($weight);

        $severeRegions = array_values(array_unique(array_filter(array_map('intval', $severeRegions))));
        $moderateRegions = array_values(array_unique(array_filter(array_map('intval', $moderateRegions))));

        $totalSevereCount = count($severeRegions);
        $totalModerateCount = count($moderateRegions);

        if ($totalSevereCount === 0 && $totalModerateCount === 0) {
            return [
                'blood_type' => $bloodTypeKey,
                'blood_type_label' => static::$bloodTypeMatrix[$bloodTypeKey]['label'] ?? "فصيلة {$bloodTypeKey}",
                'weight' => $weight,
                'weight_bracket' => $bracket,
                'duration' => 0,
                'total_price' => 0,
                'technique' => 'لم يتم اختيار مناطق ألم',
                'pressure' => static::$bloodTypeMatrix[$bloodTypeKey]['pressure'] ?? '',
                'severe_count' => 0,
                'moderate_count' => 0,
                'severe_techniques' => 0,
                'moderate_techniques' => 0,
                'total_techniques' => 0,
            ];
        }

        // Fetch Severe and Moderate protocols
        $severeProto = static::getProtocol($bloodTypeKey, 'severe', $bracket);
        $moderateProto = static::getProtocol($bloodTypeKey, 'moderate', $bracket);

        $severeTechniquesList = collect();
        if ($severeProto && !empty($severeRegions)) {
            $severeTechniquesList = $severeProto->techniques->filter(function ($tech) use ($severeRegions) {
                return in_array((int)$tech->region_number, $severeRegions, true);
            });
        }

        $moderateTechniquesList = collect();
        if ($moderateProto && !empty($moderateRegions)) {
            $moderateTechniquesList = $moderateProto->techniques->filter(function ($tech) use ($moderateRegions) {
                return in_array((int)$tech->region_number, $moderateRegions, true);
            });
        }

        $severeTechCount = count($severeTechniquesList);
        $moderateTechCount = count($moderateTechniquesList);

        // Fallbacks if database not seeded
        if ($severeTechCount === 0 && $totalSevereCount > 0) {
            $severeTechCount = $totalSevereCount * 2;
        }
        if ($moderateTechCount === 0 && $totalModerateCount > 0) {
            $moderateTechCount = $totalModerateCount * 1;
        }

        $totalTechniques = $severeTechCount + $moderateTechCount;

        // Pricing & Duration from Severe protocol (or fallback)
        $sevPrice = ($styleKey === 'intensive')
            ? ($severeProto?->luxury_price_per_technique ?? 21.0)
            : ($severeProto?->economy_price_per_technique ?? 14.0);
        $sevDuration = ($styleKey === 'intensive')
            ? ($severeProto?->luxury_duration_minutes ?? 1.5)
            : ($severeProto?->economy_duration_minutes ?? 1.0);

        // Pricing & Duration from Moderate protocol (or fallback)
        $modPrice = ($styleKey === 'intensive')
            ? ($moderateProto?->luxury_price_per_technique ?? 21.0)
            : ($moderateProto?->economy_price_per_technique ?? 14.0);
        $modDuration = ($styleKey === 'intensive')
            ? ($moderateProto?->luxury_duration_minutes ?? 1.5)
            : ($moderateProto?->economy_duration_minutes ?? 1.0);

        $totalDuration = ($severeTechCount * $sevDuration) + ($moderateTechCount * $modDuration);
        $totalPrice = ($severeTechCount * $sevPrice) + ($moderateTechCount * $modPrice);

        $matrixInfo = static::$bloodTypeMatrix[$bloodTypeKey] ?? [
            'label' => "فصيلة {$bloodTypeKey}",
            'base_technique' => 'مساج علاجي مخصص',
            'pressure' => "شدة {$severeProto?->intensity_percent}% وسرعة {$severeProto?->speed_percent}%",
        ];

        return [
            'blood_type'          => $bloodTypeKey,
            'blood_type_label'    => $matrixInfo['label'],
            'weight'              => $weight,
            'weight_bracket'      => $bracket,
            'duration'            => round($totalDuration, 1),
            'total_price'         => round($totalPrice, 2),
            'technique'           => $matrixInfo['base_technique'],
            'pressure'            => $matrixInfo['pressure'],
            'severe_count'        => $totalSevereCount,
            'moderate_count'      => $totalModerateCount,
            'severe_techniques'   => $severeTechCount,
            'moderate_techniques' => $moderateTechCount,
            'total_techniques'    => $totalTechniques,
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

        $age = 30;
        if (preg_match('/السن\s*\(([0-9]+)\)/u', $desc, $m)) {
            $age = (int)$m[1];
        }

        return [
            'protocol' => $protocol,
            'blood_type' => $bloodType,
            'weight' => $weight,
            'age' => $age,
            'severe_regions' => $severeRegions,
            'moderate_regions' => $moderateRegions,
        ];
    }

    /**
     * Build therapeutic calculation and description
     */
    public static function buildTherapeuticDescription(
        string $protocol = 'intensive',
        string $bloodType = 'O',
        float $weight = 75,
        int $age = 30,
        array $severeRegions = [],
        array $moderateRegions = [],
        bool $isUrgent = false,
        ?string $couponCode = null,
        float $couponDiscount = 0
    ): array {
        $protocol = in_array($protocol, ['intensive', 'economy']) ? $protocol : 'intensive';
        $bloodType = in_array(strtoupper($bloodType), ['A', 'B', 'AB', 'O']) ? strtoupper($bloodType) : 'O';
        $severeRegions = array_values(array_filter(array_map('intval', $severeRegions)));
        $moderateRegions = array_values(array_filter(array_map('intval', $moderateRegions)));

        $massageCalc = self::calculate($bloodType, $weight, $severeRegions, $moderateRegions, $protocol);
        $allPain = array_unique(array_merge($severeRegions, $moderateRegions));
        $chiroCalc = \App\Helpers\TherapeuticChiropracticHelper::calculate($allPain, $protocol);

        $totalDuration = (int)round($massageCalc['duration'] + $chiroCalc['duration']);
        $baseTotal = $massageCalc['total_price'] + $chiroCalc['total_price'];

        $urgentFee = 0;
        if ($isUrgent) {
            $urgentFee = (int)\App\Models\Setting::get('urgent_booking_fee', 200);
        }

        $finalPrice = max(0, $baseTotal + $urgentFee - $couponDiscount);
        $deposit = ceil($finalPrice * 0.40);

        $descParts = [];
        $protocolLabel = ($protocol === 'intensive') ? 'مكثف' : 'اقتصادي';
        $descParts[] = "نوع الجلسة: سيشن علاجية [البروتوكول: {$protocolLabel}]";
        $descParts[] = "بيانات المريض: السن ({$age}) | فصيلة الدم ({$bloodType}) | الوزن ({$weight} كجم)";
        $severeStr = !empty($severeRegions) ? implode(', ', $severeRegions) : 'لا يوجد';
        $moderateStr = !empty($moderateRegions) ? implode(', ', $moderateRegions) : 'لا يوجد';
        $descParts[] = "مناطق الألم: شديد [{$severeStr}] | متوسط [{$moderateStr}]";
        $descParts[] = "المساج العلاجي [التكنيك: {$massageCalc['technique']} | عدد التكنيكات: {$massageCalc['total_techniques']} | السعر: {$massageCalc['total_price']} ج.م | المدة: {$massageCalc['duration']} دقيقة]";
        $chiroNames = $chiroCalc['active_group_names'] ?? $chiroCalc['active_groups_names'] ?? [];
        $chiroGroupsStr = !empty($chiroNames) ? implode(' + ', $chiroNames) : 'لا يوجد';
        $descParts[] = "الكيروبراكتيك العلاجي [المناطق: {$chiroGroupsStr} | عدد التكنيكات: {$chiroCalc['total_techniques']} | السعر: {$chiroCalc['total_price']} ج.م | المدة: {$chiroCalc['duration']} دقيقة]";
        
        $hasSevere = count($severeRegions) > 0;
        if ($protocol === 'intensive') {
            $expectedSessionsPlan = $hasSevere ? '5 إلى 7 سيشن (ويفضل 3 سيشن أسبوعياً)' : '3 إلى 5 سيشن (ويفضل 2 سيشن أسبوعياً)';
        } else {
            $expectedSessionsPlan = $hasSevere ? '9 إلى 12 سيشن (ويفضل 3 سيشن أسبوعياً)' : '5 إلى 7 سيشن (ويفضل 2 سيشن أسبوعياً)';
        }
        $descParts[] = "الخطة المقترحة [عدد السيشن المتوقعة: {$expectedSessionsPlan}]";

        if ($isUrgent) {
            $descParts[] = "الحجز المستعجل [رسوم إضافية: {$urgentFee} ج.م]";
        }
        if ($couponDiscount > 0 && $couponCode) {
            $descParts[] = "كوبون الخصم [الكود: {$couponCode} | الخصم: {$couponDiscount} ج.م]";
        }

        $serviceParts = ['مساج علاجي', 'كيروبراكتيك علاجي'];

        return [
            'total_price' => $finalPrice,
            'total_duration' => $totalDuration,
            'deposit' => $deposit,
            'service_type' => implode(' + ', $serviceParts),
            'description' => implode(' | ', $descParts),
            'packages' => [$protocol],
            'massage' => $massageCalc,
            'chiro' => $chiroCalc,
            'rehab_duration' => 0,
            'rehab_price' => 0,
            'urgent_fee' => $urgentFee,
            'expected_sessions' => $expectedSessionsPlan,
        ];
    }

    /**
     * Render detailed techniques table from database
     */
    public static function renderDetailedTechniquesTable($record)
    {
        $desc = $record->description ?? $record->complaint ?? '';
        if (empty($desc)) {
            return new \Illuminate\Support\HtmlString('لا توجد تفاصيل حجز لعرض التكنيكات.');
        }

        $parsed = self::parseTherapeuticDescription($desc);
        $bloodType = in_array($parsed['blood_type'], ['A', 'B', 'AB', 'O']) ? $parsed['blood_type'] : 'O';
        $weight = $parsed['weight'];
        $bracket = self::getWeightBracket($weight);
        $protocol = $parsed['protocol'];
        $severeRegions = $parsed['severe_regions'];
        $moderateRegions = $parsed['moderate_regions'];

        $severeProto = static::getProtocol($bloodType, 'severe', $bracket);
        $moderateProto = static::getProtocol($bloodType, 'moderate', $bracket);

        $severeRows = collect();
        if ($severeProto && !empty($severeRegions)) {
            $severeRows = $severeProto->techniques->filter(function ($tech) use ($severeRegions) {
                return in_array((int)$tech->region_number, $severeRegions, true);
            });
        }

        $moderateRows = collect();
        if ($moderateProto && !empty($moderateRegions)) {
            $moderateRows = $moderateProto->techniques->filter(function ($tech) use ($moderateRegions) {
                return in_array((int)$tech->region_number, $moderateRegions, true);
            });
        }

        if ($severeRows->isEmpty() && $moderateRows->isEmpty()) {
            return new \Illuminate\Support\HtmlString('لا توجد تكنيكات مسجلة لمناطق الألم المحددة.');
        }

        $rowsHtml = '';
        $counter = 1;

        foreach ($severeRows as $r) {
            $repVal = $r->reps_display ?? ($protocol === 'intensive' ? $severeProto?->luxury_reps : $severeProto?->economy_reps);
            if (is_string($repVal) && str_contains($repVal, '/')) {
                $parts = explode('/', $repVal);
                $repVal = ($protocol === 'intensive') ? trim($parts[0]) : trim($parts[1]);
            }
            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(239, 68, 68, 0.05);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;'>🔴 شديد</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$r->region_number}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$r->region_name}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$r->massage_type}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r->tool}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r->direction}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$repVal}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #f59e0b;'>{$r->intensity_percent}%</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #a855f7;'>{$r->speed_percent}%</td>
            </tr>";
            $counter++;
        }

        foreach ($moderateRows as $r) {
            $repVal = $r->reps_display ?? ($protocol === 'intensive' ? $moderateProto?->luxury_reps : $moderateProto?->economy_reps);
            if (is_string($repVal) && str_contains($repVal, '/')) {
                $parts = explode('/', $repVal);
                $repVal = ($protocol === 'intensive') ? trim($parts[0]) : trim($parts[1]);
            }
            $rowsHtml .= "
            <tr style='border-bottom: 1px solid #334155; background: rgba(245, 158, 11, 0.05);'>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #94a3b8; font-weight: bold;'>{$counter}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155;'><span style='background: #f59e0b; color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;'>🟠 متوسط</span></td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #ff9d42;'>منطقة {$r->region_number}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: 600; color: #f8fafc;'>{$r->region_name}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #38bdf8;'>{$r->massage_type}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r->tool}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #cbd5e1;'>{$r->direction}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; font-weight: bold; color: #34d399;'>{$repVal}</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #f59e0b;'>{$r->intensity_percent}%</td>
                <td style='padding: 10px 8px; border: 1px solid #334155; color: #a855f7;'>{$r->speed_percent}%</td>
            </tr>";
            $counter++;
        }

        $protoLabel = ($protocol === 'intensive') ? 'بروتوكول مكثف (Luxury)' : 'بروتوكول اقتصادي (Economy)';
        $weightLabel = match($bracket) {
            '30_55' => '30 إلى 55 كجم',
            '55_100' => '55 إلى 100 كجم',
            '100_300' => '100 إلى 300 كجم',
            default => '55 إلى 100 كجم'
        };

        $massageCalc = self::calculate($bloodType, $weight, $severeRegions, $moderateRegions, $protocol);
        $massageDuration = $massageCalc['duration'] ?? 0;
        $massagePrice = $massageCalc['total_price'] ?? 0;

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
                    <span style='background: #1e293b; color: #38bdf8; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; border: 1px solid #0284c7;'>⏱️ مدة المساج: {$massageDuration} دقيقة</span>
                    <span style='background: #1e293b; color: #ff9d42; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; border: 1px solid #d97706;'>💰 سعر المساج: {$massagePrice} ج.م</span>
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

    public static function renderTherapeuticBodyMaps($record = null, ?array $customSevere = null, ?array $customModerate = null)
    {
        if ($customSevere !== null || $customModerate !== null) {
            $severeRegions = array_values(array_unique(array_filter(array_map('intval', (array)($customSevere ?? [])))));
            $moderateRegions = array_values(array_unique(array_filter(array_map('intval', (array)($customModerate ?? [])))));
        } else {
            $desc = $record?->description ?? $record?->complaint ?? '';
            $parsed = self::parseTherapeuticDescription($desc);
            $severeRegions = $parsed['severe_regions'];
            $moderateRegions = $parsed['moderate_regions'];
        }

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
            $stateClass = $isSelected ? 'selected' : 'unselected';
            $severeHotspots .= "<div class='therapeutic-hotspot severe-hotspot {$stateClass}' style='top: {$coord['top']}%; left: {$coord['left']}%;' data-region='{$num}' onclick='toggleTherapeuticRegion(this, \"therapeutic_severe_regions\")' title='نقر لاختيار/إلغاء المنطقة {$num} (ألم شديد)'>{$num}</div>";
        }

        // Moderate hotspots
        $moderateHotspots = '';
        foreach ($regionCoords as $num => $coord) {
            $isSelected = in_array($num, $moderateRegions);
            $stateClass = $isSelected ? 'selected' : 'unselected';
            $moderateHotspots .= "<div class='therapeutic-hotspot moderate-hotspot {$stateClass}' style='top: {$coord['top']}%; left: {$coord['left']}%;' data-region='{$num}' onclick='toggleTherapeuticRegion(this, \"therapeutic_moderate_regions\")' title='نقر لاختيار/إلغاء المنطقة {$num} (ألم متوسط)'>{$num}</div>";
        }

        $severeCount = count($severeRegions);
        $moderateCount = count($moderateRegions);

        return new \Illuminate\Support\HtmlString("
        <div style='margin-top: 15px; direction: rtl; font-family: sans-serif;'>
            <div style='background: rgba(30, 41, 59, 0.7); border: 1px solid #334155; border-radius: 10px; padding: 10px 16px; margin-bottom: 15px; color: #94a3b8; font-size: 0.9rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;'>
                <div style='display: flex; align-items: center; gap: 8px;'>
                    <span style='font-size: 1.2rem;'>👆</span>
                    <span><b>اضغط مباشرة على أي رقم داخل الصورة</b> لتحديده أو إلغائه (أحمر للشديد 🔴 | برتقالي للمتوسط 🟠).</span>
                </div>
                <div style='font-size: 0.8rem; color: #38bdf8;'>
                    ⚡ يتم تحديث القوائم والحسابات وجدول التكنيكات تلقائياً
                </div>
            </div>

            <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;'>
                <div style='background: #0f172a; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 15px; text-align: center;'>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #334155; padding-bottom: 8px;'>
                        <span style='color: #ef4444; font-weight: bold; font-size: 1rem;'>🔴 خريطة مناطق الألم الشديد (اضغط للتحديد)</span>
                        <span style='background: #ef4444; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;'>{$severeCount} منطقة</span>
                    </div>
                    <div style='position: relative; display: inline-block; max-width: 500px; width: 100%;'>
                        <img src='/images/body.jpg' alt='Severe Pain Chart' style='width: 100%; height: auto; display: block; border-radius: 8px; border: 1px solid #334155;' />
                        {$severeHotspots}
                    </div>
                </div>

                <div style='background: #0f172a; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 15px; text-align: center;'>
                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #334155; padding-bottom: 8px;'>
                        <span style='color: #f59e0b; font-weight: bold; font-size: 1rem;'>🟠 خريطة مناطق الألم المتوسط (اضغط للتحديد)</span>
                        <span style='background: #f59e0b; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;'>{$moderateCount} منطقة</span>
                    </div>
                    <div style='position: relative; display: inline-block; max-width: 500px; width: 100%;'>
                        <img src='/images/body.jpg' alt='Moderate Pain Chart' style='width: 100%; height: auto; display: block; border-radius: 8px; border: 1px solid #334155;' />
                        {$moderateHotspots}
                    </div>
                </div>
            </div>

            <style>
                .therapeutic-hotspot {
                    position: absolute;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    border: 2px solid;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 11px;
                    font-weight: 800;
                    cursor: pointer;
                    transform: translate(-50%, -50%);
                    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, border-color 0.15s ease;
                    user-select: none;
                    z-index: 10;
                }
                .therapeutic-hotspot:hover {
                    transform: translate(-50%, -50%) scale(1.35);
                    z-index: 30;
                }
                .severe-hotspot.selected {
                    background: #ef4444 !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                    box-shadow: 0 0 12px #ef4444;
                    animation: pulse-red 2s infinite;
                }
                .severe-hotspot.unselected {
                    background: rgba(15, 23, 42, 0.75);
                    border-color: rgba(239, 68, 68, 0.45);
                    color: #e2e8f0;
                }
                .severe-hotspot.unselected:hover {
                    background: rgba(239, 68, 68, 0.65);
                    border-color: #ffffff;
                    color: #ffffff;
                    box-shadow: 0 0 10px rgba(239, 68, 68, 0.8);
                }
                .moderate-hotspot.selected {
                    background: #f59e0b !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                    box-shadow: 0 0 12px #f59e0b;
                    animation: pulse-orange 2s infinite;
                }
                .moderate-hotspot.unselected {
                    background: rgba(15, 23, 42, 0.75);
                    border-color: rgba(245, 158, 11, 0.45);
                    color: #e2e8f0;
                }
                .moderate-hotspot.unselected:hover {
                    background: rgba(245, 158, 11, 0.65);
                    border-color: #ffffff;
                    color: #ffffff;
                    box-shadow: 0 0 10px rgba(245, 158, 11, 0.8);
                }
                @keyframes pulse-red {
                    0% { box-shadow: 0 0 6px rgba(239, 68, 68, 0.7); }
                    50% { box-shadow: 0 0 14px rgba(239, 68, 68, 1); }
                    100% { box-shadow: 0 0 6px rgba(239, 68, 68, 0.7); }
                }
                @keyframes pulse-orange {
                    0% { box-shadow: 0 0 6px rgba(245, 158, 11, 0.7); }
                    50% { box-shadow: 0 0 14px rgba(245, 158, 11, 1); }
                    100% { box-shadow: 0 0 6px rgba(245, 158, 11, 0.7); }
                }
            </style>

            <script>
                window.toggleTherapeuticRegion = function(el, targetField) {
                    let region = parseInt(el.dataset.region);
                    let otherField = (targetField === 'therapeutic_severe_regions') 
                        ? 'therapeutic_moderate_regions' 
                        : 'therapeutic_severe_regions';

                    // Instant client DOM visual feedback
                    let isCurrentlySelected = el.classList.contains('selected');
                    if (isCurrentlySelected) {
                        el.classList.remove('selected');
                        el.classList.add('unselected');
                    } else {
                        el.classList.remove('unselected');
                        el.classList.add('selected');
                        let otherClass = (targetField === 'therapeutic_severe_regions') ? '.moderate-hotspot' : '.severe-hotspot';
                        let otherEl = document.querySelector(otherClass + '[data-region=\"' + region + '\"]');
                        if (otherEl) {
                            otherEl.classList.remove('selected');
                            otherEl.classList.add('unselected');
                        }
                    }

                    // Robust Livewire / Alpine component discovery
                    let wireEl = el;
                    while (wireEl && wireEl !== document.body) {
                        if (wireEl.hasAttribute && wireEl.hasAttribute('wire:id')) break;
                        wireEl = wireEl.parentElement;
                    }
                    let wire = null;
                    if (wireEl && wireEl.hasAttribute && wireEl.hasAttribute('wire:id') && window.Livewire) {
                        wire = window.Livewire.find(wireEl.getAttribute('wire:id'));
                    }
                    if (!wire && window.Livewire && typeof window.Livewire.first === 'function') {
                        wire = window.Livewire.first();
                    }
                    if (!wire && typeof window.Alpine !== 'undefined' && typeof window.Alpine.\$wire === 'function') {
                        wire = window.Alpine.\$wire(el);
                    }

                    if (wire) {
                        let toNumArray = function(val) {
                            if (!val) return [];
                            if (Array.isArray(val)) return val.map(Number);
                            if (typeof val === 'object') return Object.values(val).map(Number);
                            return [Number(val)];
                        };

                        let currentTarget = toNumArray(wire.get('data.' + targetField));
                        let currentOther = toNumArray(wire.get('data.' + otherField));

                        let idx = currentTarget.indexOf(region);
                        if (idx > -1) {
                            currentTarget.splice(idx, 1);
                        } else {
                            currentTarget.push(region);
                            let otherIdx = currentOther.indexOf(region);
                            if (otherIdx > -1) {
                                currentOther.splice(otherIdx, 1);
                                if (typeof wire.set === 'function') {
                                    wire.set('data.' + otherField, currentOther, false);
                                }
                            }
                        }
                        if (typeof wire.set === 'function') {
                            wire.set('data.' + targetField, currentTarget, true);
                        }
                    }
                };
            </script>
        </div>
        ");
    }

    public static function renderTherapeuticTechniquesForForm(callable $get, $record = null)
    {
        $protocol = $get('therapeutic_protocol') ?: 'intensive';
        $bloodType = $get('therapeutic_blood_type') ?: 'O';
        $weight = (float)($get('therapeutic_weight') ?: 75);
        $age = (int)($get('therapeutic_age') ?: 30);
        $severe = array_values(array_filter(array_map('intval', (array)($get('therapeutic_severe_regions') ?: []))));
        $moderate = array_values(array_filter(array_map('intval', (array)($get('therapeutic_moderate_regions') ?: []))));

        if (empty($severe) && empty($moderate) && $record) {
            $desc = $record->description ?? $record->complaint ?? '';
            $parsed = self::parseTherapeuticDescription($desc);
            $severe = $parsed['severe_regions'];
            $moderate = $parsed['moderate_regions'];
            if (!empty($parsed['protocol'])) $protocol = $parsed['protocol'];
            if (!empty($parsed['blood_type'])) $bloodType = $parsed['blood_type'];
            if ($parsed['weight'] > 0) $weight = $parsed['weight'];
            if ($parsed['age'] > 0) $age = $parsed['age'];
        }

        if (empty($severe) && empty($moderate)) {
            return new \Illuminate\Support\HtmlString("
                <div style='background: #0f172a; border: 1px dashed #475569; border-radius: 10px; padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.95rem; margin-top: 10px; direction: rtl;'>
                    ℹ️ لم يتم تحديد مناطق ألم حتى الآن. يرجى اختيار مناطق شديدة أو متوسطة الألم من القوائم أعلاه لعرض التكنيكات المعتمدة فورياً.
                </div>
            ");
        }

        $calc = self::buildTherapeuticDescription(
            $protocol,
            $bloodType,
            $weight,
            $age,
            $severe,
            $moderate
        );

        $dummyObj = (object)[
            'description' => $calc['description'],
            'complaint' => $calc['description'],
        ];

        $massageTable = self::renderDetailedTechniquesTable($dummyObj);
        $chiroTable = \App\Helpers\TherapeuticChiropracticHelper::renderChiropracticTechniquesTable($dummyObj);

        return new \Illuminate\Support\HtmlString($massageTable->toHtml() . $chiroTable->toHtml());
    }
}
