<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Session;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Sync to Request
        VisitResource::syncRequestFromForm($data);

        // Strip virtual fields
        $virtualFields = [
            'packages', 'massage_regions', 'massage_style', 'massage_intensity',
            'cracking_type', 'cracking_regions', 'hijama_type', 'hijama_style', 'hijama_regions'
        ];
        foreach ($virtualFields as $field) {
            unset($data[$field]);
        }

        if (isset($data['client_id']) && isset($data['gender'])) {
            $client = \App\Models\Client::find($data['client_id']);
            if ($client) {
                $client->update(['gender' => $data['gender']]);
            }
        }
        return $data;
    }

    // protected function handleRecordCreation(array $data): Model
    // {
    //     $data['price'] = isset($data['Sessions']) ? $data['price']: 0;
        
    //     $record = new ($this->getModel())($data);
    //     if (
    //         static::getResource()::isScopedToTenant() &&
    //         ($tenant = Filament::getTenant())
    //     ) {
    //         return $this->associateRecordWithTenant($record, $tenant);
    //     }

    //     $record->save();
    //     if(isset($data['Sessions'])){
    //         foreach ($data['Sessions'] as $session) {
    //             Session::create([
    //                 'price' => $session['price'],
    //                 'type' => $session['type'],
    //                 'employee_id' => $session['employee_id'],
    //                 'visit_id' => $record->id,
    //             ]);
    //         }
    //     }

    //     return $record;
    // }
}
