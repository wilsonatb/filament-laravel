<?php

namespace App\Filament\Personal\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHolidays(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHolidays(Auth::user())),
            Stat::make('Total Work', $this->getTotalWork(Auth::user())),
            Stat::make('Total Pause', $this->getTotalPause(Auth::user())),
        ];
    }

    protected function getPendingHolidays(User $user)
    {
        $totalPendingHolidays = $user->holidays()->where('type', 'pending')->count();

        return $totalPendingHolidays;
    }

    protected function getApprovedHolidays(User $user)
    {
        $totalApprovedHolidays = $user->holidays()->where('type', 'approved')->count();

        return $totalApprovedHolidays;
    }

    protected function getTotalWork(User $user)
    {
        $timesheets = $user->timesheets()->where('type', 'work')->whereDate('created_at', Carbon::today())->get();

        $totalWork = 0;

        foreach ($timesheets as $timesheet) {
            $finishTime = Carbon::parse($timesheet->day_out);
            $startTime = Carbon::parse($timesheet->day_in);

            $totalDuration = $startTime->diffInSeconds($finishTime);

            $totalWork += $totalDuration;
        }

        // Convertir los segundos totales a horas y minutos
        $hours = floor($totalWork / 3600); // Segundos en una hora
        $minutes = floor(($totalWork % 3600) / 60); // Resto de segundos convertido a minutos
        $seconds = $totalWork % 60; // Resto de segundos

        // Formatear con ceros iniciales
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    protected function getTotalPause(User $user)
    {
        $timesheets = $user->timesheets()->where('type', 'pause')->whereDate('created_at', Carbon::today())->get();

        $totalWork = 0;

        foreach ($timesheets as $timesheet) {
            $finishTime = Carbon::parse($timesheet->day_out);
            $startTime = Carbon::parse($timesheet->day_in);

            $totalDuration = $startTime->diffInSeconds($finishTime);

            $totalWork += $totalDuration;
        }

        // Convertir los segundos totales a horas y minutos
        $hours = floor($totalWork / 3600); // Segundos en una hora
        $minutes = floor(($totalWork % 3600) / 60); // Resto de segundos convertido a minutos
        $seconds = $totalWork % 60; // Resto de segundos

        // Formatear con ceros iniciales
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
