<?php

namespace App\Models\Temperature;

use Illuminate\Database\Eloquent\Model;

class TemperatureSecond extends Model
{
    public $timestamps = false;
    protected $table = 'temperature_seconds';
    protected $fillable = ['value', 'received_at'];
    protected $casts = [
        'received_at' => 'datetime',
    ];
}
