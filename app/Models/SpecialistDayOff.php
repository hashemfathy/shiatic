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
        'type',
        'date',
        'day_of_week',
        'monthly_week',
        'is_full_day',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'day_of_week' => 'integer',
        'is_full_day' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get all SpecialistDayOff records that match the given specialist and date.
     *
     * @param int|string $employeeId
     * @param string $date (Y-m-d format)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMatchingSpecialistDaysOff($employeeId, string $date)
    {
        $timeVal = strtotime($date);
        if (!$timeVal || empty($employeeId)) {
            return collect();
        }

        $dayOfWeek = (int)date('w', $timeVal); // 0 (Sunday) to 6 (Saturday)
        $dayOfMonth = (int)date('d', $timeVal);

        // Occurrence in month: 1st, 2nd, 3rd, 4th, 5th
        $occurrence = (int)ceil($dayOfMonth / 7);

        // Map numeric occurrence to text
        $occurrenceMap = [
            1 => 'first',
            2 => 'second',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
        ];
        $occurrenceText = $occurrenceMap[$occurrence] ?? 'any';

        // Is it the last occurrence of this weekday in the month?
        $isLast = date('m', $timeVal) !== date('m', strtotime($date . ' +7 days'));

        return self::query()
            ->where('employee_id', $employeeId)
            ->where(function ($query) use ($date, $dayOfWeek, $occurrenceText, $isLast) {
                // 1. Specific Date
                $query->where(function ($q) use ($date) {
                    $q->where(function ($sub) {
                        $sub->where('type', 'specific_date')
                            ->orWhereNull('type');
                    })
                    ->whereDate('date', $date);
                })
                // 2. Recurring
                ->orWhere(function ($q) use ($dayOfWeek, $occurrenceText, $isLast) {
                    $q->where('type', 'recurring')
                      ->where('day_of_week', $dayOfWeek)
                      ->where(function ($q2) use ($occurrenceText, $isLast) {
                          $q2->whereNull('monthly_week')
                             ->orWhere('monthly_week', 'any')
                             ->orWhere('monthly_week', '')
                             ->orWhere('monthly_week', $occurrenceText)
                             ->when($isLast, function ($q3) {
                                 $q3->orWhere('monthly_week', 'last');
                             });
                      });
                });
            })
            ->get();
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

        $daysOff = self::getMatchingSpecialistDaysOff($employeeId, $formattedDate);

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
     * Check if the specialist has existing visits on that date/time or recurring day.
     *
     * @param int|string $employeeId
     * @param string|null $date (Y-m-d)
     * @param bool $isFullDay
     * @param string|null $startTime
     * @param string|null $endTime
     * @param string $type ('specific_date' or 'recurring')
     * @param int|null $dayOfWeek (0-6)
     * @param string|null $monthlyWeek
     * @return \Illuminate\Database\Eloquent\Collection Conflicting visits
     */
    public static function getConflictingVisits(
        $employeeId,
        $date = null,
        bool $isFullDay = true,
        ?string $startTime = null,
        ?string $endTime = null,
        string $type = 'specific_date',
        ?int $dayOfWeek = null,
        ?string $monthlyWeek = 'any'
    ) {
        if (empty($employeeId)) {
            return collect();
        }

        if ($type === 'recurring') {
            if ($dayOfWeek === null) {
                return collect();
            }

            // MySQL DAYOFWEEK: 1 = Sunday, ..., 7 = Saturday
            $mysqlDayOfWeek = $dayOfWeek + 1;

            $visits = Visit::with(['client', 'sessions'])
                ->whereDate('date', '>=', today()->toDateString())
                ->whereRaw('DAYOFWEEK(date) = ?', [$mysqlDayOfWeek])
                ->whereHas('sessions', function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                })
                ->get();

            if (!empty($monthlyWeek) && $monthlyWeek !== 'any') {
                $visits = $visits->filter(function ($visit) use ($monthlyWeek) {
                    $timeVal = strtotime($visit->date);
                    $dayOfMonth = (int)date('d', $timeVal);
                    $occurrence = (int)ceil($dayOfMonth / 7);
                    $occurrenceMap = [
                        1 => 'first',
                        2 => 'second',
                        3 => 'third',
                        4 => 'fourth',
                        5 => 'fifth',
                    ];
                    $occurrenceText = $occurrenceMap[$occurrence] ?? 'any';
                    $isLast = date('m', $timeVal) !== date('m', strtotime($visit->date . ' +7 days'));

                    if ($monthlyWeek === 'last') {
                        return $isLast;
                    }
                    return $occurrenceText === $monthlyWeek;
                });
            }

            if ($visits->isEmpty() || $isFullDay) {
                return $visits;
            }

            if (empty($startTime) || empty($endTime)) {
                return $visits;
            }

            return $visits->filter(function ($visit) use ($startTime, $endTime) {
                $visitDate = Carbon::parse($visit->date)->format('Y-m-d');
                $offStart = self::parseTimeToCarbon($visitDate, $startTime);
                $offEnd = self::parseTimeToCarbon($visitDate, $endTime);

                if (!$offStart || !$offEnd) {
                    return true;
                }

                $durationMinutes = (int)($visit->duration ?? 60);
                if ($durationMinutes <= 0) {
                    $durationMinutes = 60;
                }

                $visitStart = self::parseTimeToCarbon($visitDate, $visit->hour);
                if (!$visitStart) {
                    return true;
                }
                $visitEnd = (clone $visitStart)->addMinutes($durationMinutes);

                return $visitStart->lt($offEnd) && $visitEnd->gt($offStart);
            });
        }

        if (empty($date)) {
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
