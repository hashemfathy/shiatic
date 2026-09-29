<?php

namespace App\Filament\Resources\MassageProtocolResource\Pages;

use App\Filament\Resources\MassageProtocolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMassageProtocols extends ListRecords
{
    protected static string $resource = MassageProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
