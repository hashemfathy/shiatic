<?php

namespace App\Filament\Resources\SpecialistDayOffResource\Pages;

use App\Filament\Resources\SpecialistDayOffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialistDaysOff extends ListRecords
{
    protected static string $resource = SpecialistDayOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('تسجيل إجازة / راحة جديدة')
                ->icon('heroicon-o-plus'),
        ];
    }
}
