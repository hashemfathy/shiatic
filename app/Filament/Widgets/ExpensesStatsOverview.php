<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Session;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // always-visible stat
        if (auth()->user()?->type === 'admin') {
        $stats[] = Stat::make('Today Expenses', Expense::whereDate('created_at', Carbon::today())->sum('value'));

        // conditional stat (only for admins)
            $stats[] = Stat::make('Month Expenses', Expense::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)->sum('value'));
        }

        return $stats;
    }
}
