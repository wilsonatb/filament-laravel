<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}