<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $parsed = \App\Filament\Resources\RequestResource::parseDescription($data['description'] ?? '');
        
        // Also load massage regions from database relation
        $record = $this->getRecord();
        if ($record) {
            $parsed['massage_regions'] = $record->regions()->pluck('region_number')->toArray();
        }
        
        return array_merge($data, $parsed);
    }
}
