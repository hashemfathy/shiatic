<?php

namespace App\Filament\Resources\ChiropracticRegionResource\Pages;

use App\Filament\Resources\ChiropracticRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChiropracticRegions extends ListRecords
{
    protected static string $resource = ChiropracticRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
