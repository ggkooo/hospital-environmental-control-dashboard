<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/login', function () {
    return view('auth', [
        'action'=> 'login',
    ]);
})->name('login');

Route::get('/register', function () {
    return view('auth', [
        'action'=> 'register',
    ]);
})->name('register');

Route::post('/login', function () {
    // Handle login logic here
    return redirect()->route('home');
})->name('login.submit');

Route::post('/register', function () {
    // Handle registration logic here
    return redirect()->route('home');
})->name('register.submit');

Route::get('/', function () {
    return view('layout.base', [
        'page' => 'home',
    ]);
})->name('home');

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

Route::get('noise', function () {
    return view('layout.base', [
        'page' => 'noise',
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
