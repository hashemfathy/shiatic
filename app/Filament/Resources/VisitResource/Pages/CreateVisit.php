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
            'cracking_type', 'cracking_regions', 'hijama_type', 'hijama_style', 'hijama_regions',
            'therapeutic_protocol', 'therapeutic_blood_type', 'therapeutic_weight', 'therapeutic_age',
            'therapeutic_severe_regions', 'therapeutic_moderate_regions'
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

        // Validate specialist days off
        if (isset($data['sessions']) && is_array($data['sessions'])) {
            foreach ($data['sessions'] as $session) {
                $empId = $session['employee_id'] ?? null;
                if ($empId && !empty($data['date'])) {
                    $dayOff = \App\Models\SpecialistDayOff::getSpecialistDayOff($empId, $data['date'], $data['hour'] ?? null);
                    if ($dayOff) {
                        $employee = \App\Models\Employee::find($empId);
                        $empName = $employee?->name ?: 'المختص';
                        $timeInfo = $dayOff->is_full_day ? 'طوال اليوم' : ('من ' . ($dayOff->start_time ? date('h:i A', strtotime($dayOff->start_time)) : '') . ' إلى ' . ($dayOff->end_time ? date('h:i A', strtotime($dayOff->end_time)) : ''));
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تعارض في موعد المختص!')
                            ->body("المختص [{$empName}] غير متاح في هذا الموعد ({$timeInfo}) بتاريخ {$data['date']}. يرجى اختيار مختص آخر.")
                            ->danger()
                            ->persistent()
                            ->send();
                        $this->halt();
                    }
                }
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
