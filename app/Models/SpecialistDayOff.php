<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistDayOff extends Model
{
    use HasFactory;

    protected $table = 'specialist_days_off';

    protected $fillable = [
        'employee_id',
        'date',
        'is_full_day',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'is_full_day' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Check if a specialist is off on a specific date and time.
     *
     * @param int|string $employeeId
     * @param string $date (Y-m-d)
     * @param string|null $time (e.g. "14:00", "02:00 PM")
     * @param int $durationMinutes
     * @return SpecialistDayOff|null Returns the conflicting day off record or null if available.
     */
    public static function getSpecialistDayOff($employeeId, $date, $time = null, int $durationMinutes = 60): ?self
    {
        if (empty($employeeId) || empty($date)) {
            return null;
        }

        $formattedDate = Carbon::parse($date)->format('Y-m-d');

        $daysOff = self::where('employee_id', $employeeId)
            ->whereDate('date', $formattedDate)
            ->get();

        if ($daysOff->isEmpty()) {
            return null;
        }

        if (empty($time)) {
            return $daysOff->first();
        }

        $parsedVisitStart = self::parseTimeToCarbon($formattedDate, $time);
        if (!$parsedVisitStart) {
            return $daysOff->first();
        }
        $parsedVisitEnd = (clone $parsedVisitStart)->addMinutes($durationMinutes);

        foreach ($daysOff as $dayOff) {
            if ($dayOff->is_full_day) {
                return $dayOff;
            }

            if (!empty($dayOff->start_time) && !empty($dayOff->end_time)) {
                $offStart = self::parseTimeToCarbon($formattedDate, $dayOff->start_time);
                $offEnd = self::parseTimeToCarbon($formattedDate, $dayOff->end_time);

                if ($offStart && $offEnd) {
                    // Check overlap: visitStart < offEnd and visitEnd > offStart
                    if ($parsedVisitStart->lt($offEnd) && $parsedVisitEnd->gt($offStart)) {
                        return $dayOff;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if the specialist has existing visits on that date/time.
     *
     * @param int|string $employeeId
     * @param string $date (Y-m-d)
     * @param bool $isFullDay
     * @param string|null $startTime
     * @param string|null $endTime
     * @return \Illuminate\Database\Eloquent\Collection Conflicting visits
     */
    public static function getConflictingVisits($employeeId, $date, bool $isFullDay = true, ?string $startTime = null, ?string $endTime = null)
    {
        if (empty($employeeId) || empty($date)) {
            return collect();
        }

        $formattedDate = Carbon::parse($date)->format('Y-m-d');

        $visits = Visit::with(['client', 'sessions'])
            ->whereDate('date', $formattedDate)
            ->whereHas('sessions', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->get();

        if ($visits->isEmpty() || $isFullDay) {
            return $visits;
        }

        if (empty($startTime) || empty($endTime)) {
            return $visits;
        }

        $offStart = self::parseTimeToCarbon($formattedDate, $startTime);
        $offEnd = self::parseTimeToCarbon($formattedDate, $endTime);

        if (!$offStart || !$offEnd) {
            return $visits;
        }

        return $visits->filter(function ($visit) use ($formattedDate, $offStart, $offEnd) {
            $durationMinutes = (int)($visit->duration ?? 60);
            if ($durationMinutes <= 0) {
                $durationMinutes = 60;
            }

            $visitStart = self::parseTimeToCarbon($formattedDate, $visit->hour);
            if (!$visitStart) {
                return true; // Assume conflict if time cannot be parsed
            }
            $visitEnd = (clone $visitStart)->addMinutes($durationMinutes);

            // Overlap check
            return $visitStart->lt($offEnd) && $visitEnd->gt($offStart);
        });
    }

    /**
     * Helper to safely parse time string to Carbon on a specific date.
     */
    public static function parseTimeToCarbon(string $date, ?string $timeStr): ?Carbon
    {
        if (empty($timeStr)) {
            return null;
        }

        $timeStr = trim($timeStr);

        try {
            return Carbon::parse("{$date} {$timeStr}");
        } catch (\Exception $e) {
            return null;
        }
    }
}
