<?php

namespace App\Filament\Resources;

use Filament\Pages\Dashboard as BaseDashboard;

class DashboardPage extends BaseDashboard
{
    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->type,['admin','receptionist']);
    }
}
