<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_id',
        'type',
        'day_in',
        'day_out',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}
