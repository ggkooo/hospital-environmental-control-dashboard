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

Route::get('/temperature', function () {
    return view('layout.base', [
        'page' => 'temperature',
    ]);
});

Route::get('/humidity', function () {
    return view('layout.base', [
        'page' => 'humidity',
    ]);
});

Route::post('/lang/change', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale');
    $availableLocales = config('app.supported_locales', []);
    if (in_array($locale, $availableLocales)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.change');
