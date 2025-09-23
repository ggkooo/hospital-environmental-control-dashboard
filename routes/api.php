<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\TemperatureController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ApiController::class, 'handle']);

Route::get('temperature', [TemperatureController::class, 'index']);
Route::get('temperature/available-dates', [TemperatureController::class, 'availableDates']);
