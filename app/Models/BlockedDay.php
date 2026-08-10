<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedDay extends Model
{
    use HasFactory;

    protected $table = 'blocked_days';

    protected $fillable = [
        'label',
        'type',
        'specific_date',
        'day_of_week',
        'monthly_week',
        'start_time',
        'end_time',
    ];

    /**
     * Get all BlockedDay records that match the given date.
     *
     * @param string $date (Y-m-d format)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMatchingBlockedDays(string $date)
    {
        $timeVal = strtotime($date);
        if (!$timeVal) {
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
            ->where(function ($query) use ($date, $dayOfWeek, $occurrenceText, $isLast) {
                // 1. Specific Date
                $query->where(function ($q) use ($date) {
                    $q->where('type', 'specific_date')
                      ->whereDate('specific_date', $date);
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
     * Check if a given date is completely blocked (full-day block).
     *
     * @param string $date (Y-m-d format)
     * @return bool
     */
    public static function isDateBlocked(string $date): bool
    {
        $matches = self::getMatchingBlockedDays($date);
        foreach ($matches as $match) {
            if (is_null($match->start_time) && is_null($match->end_time)) {
                return true;
            }
        }
        return false;
    }
}
