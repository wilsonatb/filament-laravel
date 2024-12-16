<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_id',
        'day',
        'type',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}
