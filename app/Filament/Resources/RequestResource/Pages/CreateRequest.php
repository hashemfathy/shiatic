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
