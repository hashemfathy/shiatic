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

        $regionRepetitions = [
            1 => 2, 2 => 1, 3 => 2, 4 => 3, 5 => 2, 6 => 1, 7 => 2, 8 => 3,
            9 => 1, 10 => 1, 11 => 1, 12 => 1, 13 => 1, 14 => 1, 15 => 1, 16 => 1,
            17 => 1, 18 => 1, 19 => 1, 20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1,
            25 => 2, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
            31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1,
            37 => 1, 38 => 2, 39 => 2
        ];

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
            $regionRepetitions
        );

        $data['service_type'] = $built['service_type'];
        $data['description'] = $built['description'];

        // If dates_times is empty, fallback
        if (empty($bookings)) {
            $data['deposit'] = $pricing['deposit'];
            $record = static::getModel()::create($data);
            $this->syncRegions($record, $data['massage_regions'] ?? []);
            return $record;
        }

        foreach ($bookings as $booking) {
            $recordData = $data;
            unset($recordData['dates_times']); // remove repeater data
            $recordData['date'] = $booking['date'];
            $recordData['time'] = $booking['time'];
            $recordData['deposit'] = $booking['deposit'] ?? $pricing['deposit'];
            
            $record = static::getModel()::create($recordData);
            $this->syncRegions($record, $data['massage_regions'] ?? []);
        }

        return $record;
    }

    protected function syncRegions($record, array $massageRegions): void
    {
        $regionRepetitions = [
            1 => 2, 2 => 1, 3 => 2, 4 => 3, 5 => 2, 6 => 1, 7 => 2, 8 => 3,
            9 => 1, 10 => 1, 11 => 1, 12 => 1, 13 => 1, 14 => 1, 15 => 1, 16 => 1,
            17 => 1, 18 => 1, 19 => 1, 20 => 1, 21 => 1, 22 => 1, 23 => 1, 24 => 1,
            25 => 2, 26 => 3, 27 => 2, 28 => 2, 29 => 3, 30 => 2,
            31 => 1, 32 => 1, 33 => 1, 34 => 1, 35 => 1, 36 => 1,
            37 => 1, 38 => 2, 39 => 2
        ];
        
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
