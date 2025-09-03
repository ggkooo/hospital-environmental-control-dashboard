<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('layout.base', [
        'page' => 'home',
    ]);
});
