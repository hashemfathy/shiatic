<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $bookings = $data['dates_times'] ?? [];
        $record = null;

        if (($data['booking_type'] ?? 'وقائية') === 'موعد مع مختص') {
            $age = (int)($data['consultation_age'] ?? 30);
            $weight = (float)($data['consultation_weight'] ?? 70);
            $bloodType = $data['consultation_blood_type'] ?? 'O';
            $notes = trim($data['consultation_notes'] ?? '');
            $isUrgent = (bool)($data['is_urgent'] ?? false);
            $urgentFee = $isUrgent ? (int)\App\Models\Setting::get('urgent_booking_fee', 200) : 0;
            
            $price = 200.0 + $urgentFee;
            $couponCode = $data['coupon_code'] ?? null;
            $couponDiscount = (float)($data['coupon_discount'] ?? 0);
            if ($couponDiscount > 0) {
                $price = max(0, $price - $couponDiscount);
            }
            $duration = 15;
            $deposit = (int)$price;

            $descParts = [];
            $descParts[] = "نوع الجلسة: موعد مع مختص [استشارة]";
            $descParts[] = "بيانات المريض: السن ({$age}) | فصيلة الدم ({$bloodType}) | الوزن ({$weight} كجم)";
            if (!empty($notes)) {
                $descParts[] = "الشكوى أو سبب الاستشارة: {$notes}";
            }
            $descParts[] = "مدة الموعد: 15 دقيقة | السعر: 200.00 ج.م";
            if ($isUrgent) {
                $descParts[] = "الحجز المستعجل [رسوم إضافية: {$urgentFee} ج.م]";
            }
            if ($couponDiscount > 0 && !empty($couponCode)) {
                $descParts[] = "كوبون الخصم [الكود: {$couponCode} | الخصم: {$couponDiscount} ج.م]";
            }

            $data['total_price'] = $price;
            $data['total_duration'] = $duration;
            $data['deposit'] = $deposit;
            $data['service_type'] = 'موعد مع مختص';
            $data['packages'] = ['consultation'];
            $data['description'] = implode(' | ', $descParts);

            if (empty($bookings)) {
                return static::getModel()::create($data);
            }

            foreach ($bookings as $booking) {
                $recordData = $data;
                unset($recordData['dates_times']);
                $recordData['date'] = $booking['date'];
                $recordData['time'] = $booking['time'];
                $recordData['deposit'] = $booking['deposit'] ?? $deposit;
                $record = static::getModel()::create($recordData);
            }

            return $record;
        }

        if (($data['booking_type'] ?? 'وقائية') === 'علاجية') {
            $calc = \App\Helpers\TherapeuticMassageHelper::buildTherapeuticDescription(
                $data['therapeutic_protocol'] ?? 'intensive',
                $data['therapeutic_blood_type'] ?? 'O',
                (float)($data['therapeutic_weight'] ?? 75),
                (int)($data['therapeutic_age'] ?? 30),
                (array)($data['therapeutic_severe_regions'] ?? []),
                (array)($data['therapeutic_moderate_regions'] ?? []),
                (bool)($data['is_urgent'] ?? false),
                $data['coupon_code'] ?? null,
                (float)($data['coupon_discount'] ?? 0)
            );

            $data['total_price'] = $calc['total_price'];
            $data['total_duration'] = $calc['total_duration'];
            $data['deposit'] = $calc['deposit'];
            $data['service_type'] = $calc['service_type'];
            $data['packages'] = $calc['packages'];
            $data['description'] = $calc['description'];

            if (empty($bookings)) {
                $record = static::getModel()::create($data);
                $this->syncTherapeuticRegions($record, $data);
                return $record;
            }

            foreach ($bookings as $booking) {
                $recordData = $data;
                unset($recordData['dates_times']);
                $recordData['date'] = $booking['date'];
                $recordData['time'] = $booking['time'];
                $recordData['deposit'] = $booking['deposit'] ?? $calc['deposit'];
                
                $record = static::getModel()::create($recordData);
                $this->syncTherapeuticRegions($record, $data);
            }

            return $record;
        }

        $packages = $data['packages'] ?? [];
        $style = 'economy';
        if (in_array('intensive', $packages)) {
            $style = 'intensive';
        } elseif (in_array('economy', $packages)) {
            $style = 'economy';
        } else {
            $style = $data['massage_style'] ?? 'intensive';
        }
        $regionRepetitions = \App\Helpers\MassageHelper::getRegionRepetitions($style);

        // 1. Rebuild details on the data array first
        $pricing = \App\Filament\Resources\RequestResource::calculatePricing($data);
        $data['total_price'] = $pricing['total_price'];
        $data['total_duration'] = $pricing['total_duration'];

        $built = \App\Filament\Resources\RequestResource::buildDescription(
            $data['booking_type'] ?? 'وقائية',
            $data['packages'] ?? [],
            $data['massage_regions'] ?? [],
            $data['massage_style'] ?? 'intensive',
            $data['massage_intensity'] ?? 'medium',
            $data['cracking_type'] ?? 'none',
            $data['cracking_regions'] ?? [],
            $data['hijama_type'] ?? 'none',
            $data['hijama_style'] ?? 'intensive',
            $data['hijama_regions'] ?? [],
            $regionRepetitions,
            $data['cracking_style'] ?? 'intensive'
        );

        $data['service_type'] = $built['service_type'];
        $data['description'] = $built['description'];

        // If dates_times is empty, fallback
        if (empty($bookings)) {
            $data['deposit'] = $pricing['deposit'];
            $record = static::getModel()::create($data);
            $this->syncRegions($record, $data['massage_regions'] ?? [], $style);
            return $record;
        }

        foreach ($bookings as $booking) {
            $recordData = $data;
            unset($recordData['dates_times']); // remove repeater data
            $recordData['date'] = $booking['date'];
            $recordData['time'] = $booking['time'];
            $recordData['deposit'] = $booking['deposit'] ?? $pricing['deposit'];
            
            $record = static::getModel()::create($recordData);
            $this->syncRegions($record, $data['massage_regions'] ?? [], $style);
        }

        return $record;
    }

    protected function syncTherapeuticRegions($record, array $data): void
    {
        $bloodTypeKey = in_array(strtoupper($data['therapeutic_blood_type'] ?? 'O'), ['A', 'B', 'AB', 'O']) ? strtoupper($data['therapeutic_blood_type']) : 'O';
        $bracket = \App\Helpers\TherapeuticMassageHelper::getWeightBracket((float)($data['therapeutic_weight'] ?? 75));
        $sevRepMap = \App\Helpers\TherapeuticMassageHelper::$severeTechniqueMap[$bloodTypeKey][$bracket] ?? [];
        $modRepMap = \App\Helpers\TherapeuticMassageHelper::$moderateTechniqueMap ?? [];

        $severeRegions = (array)($data['therapeutic_severe_regions'] ?? []);
        $moderateRegions = (array)($data['therapeutic_moderate_regions'] ?? []);

        $record->regions()->delete();
        foreach ($severeRegions as $rNum) {
            $rNum = (int)$rNum;
            $record->regions()->create([
                'region_number' => $rNum,
                'repetitions' => $sevRepMap[$rNum] ?? 1,
            ]);
        }
        foreach ($moderateRegions as $rNum) {
            $rNum = (int)$rNum;
            $record->regions()->create([
                'region_number' => $rNum,
                'repetitions' => $modRepMap[$rNum] ?? 1,
            ]);
        }
    }

    protected function syncRegions($record, array $massageRegions, string $style): void
    {
        $regionRepetitions = \App\Helpers\MassageHelper::getRegionRepetitions($style);
        
        $record->regions()->delete();
        foreach ($massageRegions as $rNum) {
            $rNum = (int)$rNum;
            if (isset($regionRepetitions[$rNum])) {
                $record->regions()->create([
                    'region_number' => $rNum,
                    'repetitions' => $regionRepetitions[$rNum],
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
