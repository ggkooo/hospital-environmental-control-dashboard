<?php

namespace App\Models\Humidity;

use Illuminate\Database\Eloquent\Model;

class HumiditySecond extends Model
{
    public $timestamps = false;
    protected $table = 'humidity_seconds';
    protected $fillable = ['value', 'received_at'];
    protected $casts = [
        'received_at' => 'datetime',
    ];
}

