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
        $parsed = \App\Filament\Resources\RequestResource::parseDescription($data['description'] ?? '');
        
        // Also load massage regions from database relation if parsed massage regions is empty
        $record = $this->getRecord();
        if ($record && empty($parsed['massage_regions'])) {
            $parsed['massage_regions'] = $record->regions()->pluck('region_number')->toArray();
        }

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
        
        return array_merge($data, $parsed);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate pricing totals based on selections
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
        
        // Sync massage regions relation in database
        $record->regions()->delete();
        
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
