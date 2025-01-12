<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $user = User::find($record->user_id);
        $recipe = $user;
        
        // send email only if approved
        if ($record->type === 'approved') {
            $dataToSend = [
                'day' => $record->day,
                'name' => $user->name,
                'email' => $user->email,
            ];
            Mail::to($record->user)->send(new \App\Mail\HolidayApproved($dataToSend));

            Notification::make()
                ->success()
                ->title('Solicitud de vacaciones')
                ->body('El dia "'.$record->day.'" ha sido aprobado.')
                ->sendToDatabase($recipe);
        }

        if ($record->type === 'decline') {
            $dataToSend = [
                'day' => $record->day,
                'name' => $user->name,
                'email' => $user->email,
            ];
            Mail::to($record->user)->send(new \App\Mail\HolidayDecline($dataToSend));

            Notification::make()
                ->success()
                ->title('Solicitud de vacaciones')
                ->danger()
                ->body('El dia "'.$record->day.'" ha sido rechazado.')
                ->sendToDatabase($recipe);
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
