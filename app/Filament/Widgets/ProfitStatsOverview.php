<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Session;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProfitStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // always-visible stat
        if (auth()->user()?->type === 'admin') {
        $stats[] = Stat::make('Today Net Profit',(Visit::whereDate('date', Carbon::today())->sum('paid')) - (Expense::whereDate('created_at', Carbon::today())->sum('value')));
        
        // conditional stat (only for admins)
            $stats[] = Stat::make('Month Net Profit', (Visit::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)->sum('paid')) -
            (Expense::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)->sum('value')));
        }

        return $stats;
    }
}
