<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Session;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // always-visible stat
        if (auth()->user()?->type === 'admin') {
        $stats[] = Stat::make('Today Income',Visit::whereDate('date', Carbon::today())->sum('paid'));

        // conditional stat (only for admins)
            $stats[] = Stat::make('Month Income', Visit::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)->sum('paid') );
        }

        return $stats;
    }
}
