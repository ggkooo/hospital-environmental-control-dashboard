<?php

namespace App\Models\Noise;

use Illuminate\Database\Eloquent\Model;

class NoiseSecond extends Model
{
    public $timestamps = false;
    protected $table = 'noise_seconds';
    protected $fillable = ['value', 'received_at'];
    protected $casts = [
        'received_at' => 'datetime',
    ];
}

