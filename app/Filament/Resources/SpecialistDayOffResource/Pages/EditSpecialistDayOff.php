<?php

namespace App\Filament\Resources\SpecialistDayOffResource\Pages;

use App\Filament\Resources\SpecialistDayOffResource;
use App\Models\SpecialistDayOff;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSpecialistDayOff extends EditRecord
{
    protected static string $resource = SpecialistDayOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $isSpecialist = SpecialistDayOffResource::isSpecialist();
        if (empty($data['employee_id'])) {
            $currentEmpId = SpecialistDayOffResource::getCurrentEmployeeId();
            $data['employee_id'] = $currentEmpId ?: $this->record->employee_id;
        }

        $employeeId = $data['employee_id'];
        $type = $data['type'] ?? 'specific_date';
        $date = $data['date'] ?? null;
        $dayOfWeek = isset($data['day_of_week']) ? (int)$data['day_of_week'] : null;
        $monthlyWeek = $data['monthly_week'] ?? 'any';
        $isFullDay = (bool)($data['is_full_day'] ?? true);
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;

        if ($type === 'recurring') {
            $data['date'] = null;
        } else {
            $data['day_of_week'] = null;
            $data['monthly_week'] = null;
        }

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
            $endTime,
            $type,
            $dayOfWeek,
            $monthlyWeek
        );

        if ($conflicts->isNotEmpty()) {
            $conflictList = $conflicts->map(function ($visit) {
                $clientName = $visit->client?->name ?: 'عميل';
                $visitDate = $visit->date ?: '';
                $time = $visit->hour ?: 'غير محدد';
                return "• زيارة العميل [{$clientName}] بتاريخ ({$visitDate}) الساعة ({$time})";
            })->implode("\n");

            Notification::make()
                ->title('لا يمكن تعديل الإجازة إلى هذا الوقت!')
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
