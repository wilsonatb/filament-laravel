<?php

namespace App\Imports;

use App\Models\Calendar;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MyTimeSheetImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            try {
                $calendar = Calendar::where('name', $row['calendario'])->first();

                if (is_null($calendar)) {
                    throw new \Exception("Calendar not found: " . $row['calendario']);
                }

                Timesheet::create([
                    'calendar_id' => $calendar->id,
                    'user_id' => Auth::user()->id,
                    'type' => $row['tipo'],
                    'day_in' => $row['hora_entrada'],
                    'day_out' => $row['hora_salida'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error processing row: ' . json_encode($row) . ' | Error: ' . $e->getMessage());
            }
        }
    }
}
