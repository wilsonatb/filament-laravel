<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lasTimeSheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if ($lasTimeSheet == null) {
            return [
                Action::make('inWork')
                    ->label('Entrar a trabajar')
                    ->color('success')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->requiresConfirmation()
                    ->action(function () {
                        $user = Auth::user();
                        $timeSheet = new Timesheet();
                        $timeSheet->user_id = $user->id;
                        $timeSheet->calendar_id = 1;
                        $timeSheet->type = 'work';
                        $timeSheet->day_in = now();
                        $timeSheet->save();
                    }),
                Actions\CreateAction::make(),
            ];
        }

        return [
            Action::make('inWork')
                ->label('Entrar a trabajar')
                ->color('success')
                ->disabled($lasTimeSheet->day_out == null)
                ->visible($lasTimeSheet->day_out != null)
                ->keyBindings(['command+s', 'ctrl+s'])
                ->requiresConfirmation()
                ->action(function () {
                    $user = Auth::user();
                    $timeSheet = new Timesheet();
                    $timeSheet->user_id = $user->id;
                    $timeSheet->calendar_id = 1;
                    $timeSheet->type = 'work';
                    $timeSheet->day_in = now();
                    $timeSheet->save();
                }),
            Action::make('stopWork')
                ->label('Parar de trabajar')
                ->color('success')
                ->disabled($lasTimeSheet->type == 'work' && $lasTimeSheet->day_out != null)
                ->visible($lasTimeSheet->type == 'work' && $lasTimeSheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lasTimeSheet) {
                    $lasTimeSheet->day_out = now();
                    $lasTimeSheet->save();
                }),
            Action::make('inPause')
                ->label('Comenzar pausar')
                ->color('info')
                ->disabled($lasTimeSheet->type == 'work' && $lasTimeSheet->day_out != null)
                ->visible($lasTimeSheet->type == 'work' && $lasTimeSheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lasTimeSheet) {
                    $lasTimeSheet->day_out = now();
                    $lasTimeSheet->save();
                    $timeSheet = new Timesheet();
                    $timeSheet->user_id = Auth::user()->id;
                    $timeSheet->calendar_id = 1;
                    $timeSheet->type = 'pause';
                    $timeSheet->day_in = now();
                    $timeSheet->save();
                }),
            Action::make('stopPause')
                ->label('Parar pausa')
                ->color('info')
                ->disabled($lasTimeSheet->type == 'pause' && $lasTimeSheet->day_out != null)
                ->visible($lasTimeSheet->type == 'pause' && $lasTimeSheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lasTimeSheet) {
                    $lasTimeSheet->day_out = now();
                    $lasTimeSheet->save();
                    $timeSheet = new Timesheet();
                    $timeSheet->user_id = Auth::user()->id;
                    $timeSheet->calendar_id = 1;
                    $timeSheet->type = 'work';
                    $timeSheet->day_in = now();
                    $timeSheet->save();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
