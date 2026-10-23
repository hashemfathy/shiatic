<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisits extends ListRecords
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('room_rental')
                ->label('إيجار غرفة للمختص')
                ->icon('heroicon-o-home-modern')
                ->color('success')
                ->url(\App\Filament\Pages\RoomRentalPage::getUrl())
                ->visible(fn () => in_array(auth()->user()?->type, ['admin', 'specialist', 'sr.specialist'])),
            Actions\CreateAction::make(),
        ];
    }
}
