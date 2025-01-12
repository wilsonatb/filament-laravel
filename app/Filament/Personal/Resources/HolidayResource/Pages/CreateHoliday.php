<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'pending';

        $userAdmin = User::find(1);
        $dataToSend = [
            'day' => $data['day'],
            'name' => User::find($data['user_id'])->name,
            'email' => User::find($data['user_id'])->email,
        ];
        Mail::to($userAdmin)->send(new \App\Mail\HolidayPending($dataToSend));

        // notification for admin
        Notification::make()
            ->info()
            ->title('Solicitud de vacaciones')
            ->body('El usuario "'.$dataToSend['name'].'" ha solicitado vacaciones para el dia "'.$dataToSend['day'].'".')
            ->sendToDatabase($userAdmin);

        $recipe = Auth::user();

        Notification::make()
            ->success()
            ->title('Solicitud de vacaciones')
            ->body('Tu solicitud debe ser aprobada por el administrador.')
            ->sendToDatabase($recipe);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
