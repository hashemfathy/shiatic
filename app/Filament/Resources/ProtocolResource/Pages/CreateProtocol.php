<?php

namespace App\Filament\Resources\ProtocolResource\Pages;

use App\Filament\Resources\ProtocolResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProtocol extends CreateRecord
{
    protected static string $resource = ProtocolResource::class;

}
