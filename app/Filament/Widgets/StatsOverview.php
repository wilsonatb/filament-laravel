<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type','pending')->count();
        $totalTimesheets = Timesheet::all()->count();
        return [
            Stat::make('Employess', $totalEmployees),
            Stat::make('Pending holidays', $totalHolidays),
            Stat::make('Timesheets', $totalTimesheets),
            /* Stat::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 199])
            ->color('success'), */
        ];
    }
}
