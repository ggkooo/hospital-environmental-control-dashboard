<?php

namespace App\Models\Humidity;

use Illuminate\Database\Eloquent\Model;

class HumidityMinute extends Model
{
    public $timestamps = false;
    protected $table = 'humidity_minutes';
    protected $fillable = ['average_value', 'minute'];
    protected $casts = [
        'minute' => 'datetime',
    ];
}

