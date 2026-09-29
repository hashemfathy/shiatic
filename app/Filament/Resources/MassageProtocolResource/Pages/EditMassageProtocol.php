<?php

namespace App\Filament\Resources\MassageProtocolResource\Pages;

use App\Filament\Resources\MassageProtocolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMassageProtocol extends EditRecord
{
    protected static string $resource = MassageProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
