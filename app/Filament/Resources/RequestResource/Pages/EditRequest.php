<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $bookingType = $data['booking_type'] ?? 'وقائية';

        // Normalize time if it is a legacy 12-hour format string (e.g. 01:00 to 08:30)
        if (isset($data['time'])) {
            $parts = explode(':', $data['time']);
            if (count($parts) >= 2) {
                $hrs = (int)$parts[0];
                if ($hrs >= 1 && $hrs <= 8) {
                    $data['time'] = sprintf('%02d:%02d', $hrs + 12, (int)$parts[1]);
                }
            }
        }

        if ($bookingType === 'علاجية') {
            $parsed = \App\Helpers\TherapeuticMassageHelper::parseTherapeuticDescription($data['description'] ?? '');
            
            $record = $this->getRecord();
            if ($record) {
                $dbRegions = $record->regions()->pluck('region_number')->toArray();
                if (empty($parsed['severe_regions']) && empty($parsed['moderate_regions']) && !empty($dbRegions)) {
                    $parsed['severe_regions'] = $dbRegions;
                }
            }

            $data['therapeutic_protocol'] = $parsed['protocol'] ?? ($data['packages'][0] ?? 'intensive');
            $data['therapeutic_blood_type'] = $parsed['blood_type'] ?? 'O';
            $data['therapeutic_weight'] = $parsed['weight'] ?? 75;
            $data['therapeutic_age'] = $parsed['age'] ?? 30;
            $data['therapeutic_severe_regions'] = array_values(array_map('intval', $parsed['severe_regions'] ?? []));
            $data['therapeutic_moderate_regions'] = array_values(array_map('intval', $parsed['moderate_regions'] ?? []));

            return $data;
        }

        $parsed = \App\Filament\Resources\RequestResource::parseDescription($data['description'] ?? '');
        
        // Also load massage regions from database relation if parsed massage regions is empty
        $record = $this->getRecord();
        if ($record && empty($parsed['massage_regions'])) {
            $parsed['massage_regions'] = $record->regions()->pluck('region_number')->toArray();
        }
        
        return array_merge($data, $parsed);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $bookingType = $data['booking_type'] ?? 'وقائية';

        if ($bookingType === 'علاجية') {
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
            if (!isset($data['deposit']) || $data['deposit'] === null || $data['deposit'] === '') {
                $data['deposit'] = $calc['deposit'];
            }
            $data['service_type'] = $calc['service_type'];
            $data['packages'] = $calc['packages'];
            $data['description'] = $calc['description'];

            return $data;
        }

        // Calculate pricing totals based on selections (preventative)
        $pricing = \App\Filament\Resources\RequestResource::calculatePricing($data);
        $data['total_price'] = $pricing['total_price'];
        $data['total_duration'] = $pricing['total_duration'];
        if (!isset($data['deposit']) || $data['deposit'] === null || $data['deposit'] === '') {
            $data['deposit'] = $pricing['deposit'];
        }

        // Rebuild description and service_type
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

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $data = $this->form->getRawState();
        $bookingType = $data['booking_type'] ?? $record->booking_type ?? 'وقائية';
        
        // Sync regions relation in database
        $record->regions()->delete();

        if ($bookingType === 'علاجية') {
            $bloodTypeKey = in_array(strtoupper($data['therapeutic_blood_type'] ?? 'O'), ['A', 'B', 'AB', 'O']) ? strtoupper($data['therapeutic_blood_type']) : 'O';
            $bracket = \App\Helpers\TherapeuticMassageHelper::getWeightBracket((float)($data['therapeutic_weight'] ?? 75));
            $sevRepMap = \App\Helpers\TherapeuticMassageHelper::$severeTechniqueMap[$bloodTypeKey][$bracket] ?? [];
            $modRepMap = \App\Helpers\TherapeuticMassageHelper::$moderateTechniqueMap ?? [];

            $severeRegions = (array)($data['therapeutic_severe_regions'] ?? []);
            $moderateRegions = (array)($data['therapeutic_moderate_regions'] ?? []);

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
            return;
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
        
        $massageRegions = isset($data['massage_regions']) ? (array)$data['massage_regions'] : [];
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
}
