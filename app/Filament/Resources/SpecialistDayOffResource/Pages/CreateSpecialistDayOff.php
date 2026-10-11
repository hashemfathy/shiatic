<?php

namespace App\Filament\Resources\SpecialistDayOffResource\Pages;

use App\Filament\Resources\SpecialistDayOffResource;
use App\Models\SpecialistDayOff;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialistDayOff extends CreateRecord
{
    protected static string $resource = SpecialistDayOffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->type !== 'admin' || empty($data['employee_id'])) {
            $data['employee_id'] = SpecialistDayOffResource::getCurrentEmployeeId();
        }

        $employeeId = $data['employee_id'];
        $date = $data['date'];
        $isFullDay = (bool)($data['is_full_day'] ?? true);
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;

        if (!$isFullDay && (!empty($startTime) && !empty($endTime))) {
            if (strtotime($startTime) >= strtotime($endTime)) {
                Notification::make()
                    ->title('خطأ في تحديد الساعات')
                    ->body('يجب أن يكون وقت الانتهاء بعد وقت البدء.')
                    ->danger()
                    ->send();
                $this->halt();
            }
        }

        // Check for conflicting scheduled visits
        $conflicts = SpecialistDayOff::getConflictingVisits(
            $employeeId,
            $date,
            $isFullDay,
            $startTime,
            $endTime
        );

        if ($conflicts->isNotEmpty()) {
            $conflictList = $conflicts->map(function ($visit) {
                $clientName = $visit->client?->name ?: 'عميل';
                $time = $visit->hour ?: 'غير محدد';
                return "• زيارة العميل [{$clientName}] في تمام الساعة ({$time})";
            })->implode("\n");

            Notification::make()
                ->title('لا يمكن تحديد إجازة في هذا الوقت!')
                ->body("توجد زيارات مجدولة مسبقاً مسندة للمختص في هذا اليوم/الوقت:\n{$conflictList}\nيرجى إعادة جدولة أو إلغاء الزيارات أولاً.")
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
