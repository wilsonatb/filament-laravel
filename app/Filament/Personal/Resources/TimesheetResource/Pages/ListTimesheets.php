<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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

                        Notification::make()
                            ->title('Has entrado a trabajar')
                            ->body('Has comenzado a trabajar a las:' . Carbon::now())
                            ->color('success')
                            ->success()
                            ->send();
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

                    Notification::make()
                        ->title('Has entrado a trabajar')
                        ->body('Has comenzado a trabajar a las:' . Carbon::now())
                        ->color('success')
                        ->success()
                        ->send();
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

                    Notification::make()
                        ->title('Has parado de trabajar')
                        ->body('Has parado de trabajar a las:' . Carbon::now())
                        ->color('success')
                        ->success()
                        ->send();
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

                    Notification::make()
                        ->title('Comienzas tu pausa')
                        ->body('Has comenzado tu pausa a las:' . Carbon::now())
                        ->color('info')
                        ->info()
                        ->send();
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

                    Notification::make()
                        ->title('Has vuelto a trabajar')
                        ->body('Has vuelto a trabajar a las:' . Carbon::now())
                        ->color('info')
                        ->info()
                        ->send();
                }),
            Actions\CreateAction::make(),
            Action::make('createPDF')
                ->label('Crear PDF')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    $timesheets = Timesheet::where('user_id', Auth::user()->id)->get();
                    $pdf = PDF::loadView('pdf.timesheet', ['timesheets' => $timesheets]);
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'timesheet.pdf');
                }),
        ];
    }
}
