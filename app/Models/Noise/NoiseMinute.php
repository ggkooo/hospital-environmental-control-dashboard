<?php

namespace App\Models\Noise;

use Illuminate\Database\Eloquent\Model;

class NoiseMinute extends Model
{
    public $timestamps = false;
    protected $table = 'noise_minutes';
    protected $fillable = ['average_value', 'minute'];
    protected $casts = [
        'minute' => 'datetime',
    ];
}
