<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\IsAuthenticated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

//Route::get('/', function () {
//    return view('welcome');
//});

// AUTHENTICATION
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'store'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');






Route::get('/', function () {
    return view('layout.base', [
        'page' => 'home',
    ]);
})->middleware(IsAuthenticated::class)->name('home');

Route::get('/temperature', function () {
    return view('layout.base', [
        'page' => 'temperature',
    ]);
})->middleware(IsAuthenticated::class);

Route::get('/humidity', function () {
    return view('layout.base', [
        'page' => 'humidity',
    ]);
})->middleware(IsAuthenticated::class);

Route::get('noise', function () {
    return view('layout.base', [
        'page' => 'noise',
    ]);
})->middleware(IsAuthenticated::class)->name('noise');

Route::post('/lang/change', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale');
    $availableLocales = config('app.supported_locales', []);
    if (in_array($locale, $availableLocales)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.change');
