<?php

namespace App\Filament\Resources\ProtocolResource\Pages;

use App\Filament\Resources\ProtocolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProtocol extends ViewRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
