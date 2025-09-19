<?php

namespace App\Models\Temperature;

use Illuminate\Database\Eloquent\Model;

class TemperatureMinute extends Model
{
    public $timestamps = false;
    protected $table = 'temperature_minutes';
    protected $fillable = ['average_value', 'minute'];
    protected $casts = [
        'minute' => 'datetime',
    ];
}

