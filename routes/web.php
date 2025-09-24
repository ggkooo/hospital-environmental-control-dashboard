<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TemperatureController;
use App\Http\Middleware\IsAuthenticated;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Hash;

//Route::get('/', function () {
//    return view('welcome');
//});

// AUTHENTICATION
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'store'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// USERS ADMINISTRATION
Route::get('/admin/users', function () {
    $users = User::all();
    return view('layout.base', [
        'page' => 'admin.users',
        'users' => $users,
    ]);
})->name('users.index');

Route::get('/admin/users/{user}/edit', function (User $user) {
    return view('layout.base', [
        'page' => 'admin.edit-user',
        'user' => $user,
    ]);
})->name('users.edit');

Route::delete('/admin/users/{user}', function (User $user) {
    $user->delete();
    return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
})->name('users.destroy');

Route::post('/admin/users/{user}/verify', function (User $user) {
    $user->is_validated = 1;
    $user->save();
    return redirect()->route('users.index')->with('success', 'Usuário verificado com sucesso!');
})->name('users.verify');

Route::post('/admin/users/{user}/make-admin', function (User $user) {
    $user->is_admin = 1;
    $user->save();
    return redirect()->route('users.index')->with('success', 'Usuário agora é administrador!');
})->name('users.makeAdmin');

Route::post('/admin/users/{user}/block', function (User $user) {
    $user->is_blocked = 1;
    $user->save();
    return redirect()->route('users.index')->with('success', 'Usuário bloqueado com sucesso!');
})->name('users.block');

Route::post('/admin/users/{user}/unblock', function (User $user) {
    $user->is_blocked = 0;
    $user->save();
    return redirect()->route('users.index')->with('success', 'Usuário desbloqueado com sucesso!');
})->name('users.unblock');

Route::post('/admin/users/{user}/toggle-admin', function (User $user) {
    $user->is_admin = $user->is_admin ? 0 : 1;
    $user->save();
    return redirect()->route('users.index')->with('success', $user->is_admin ? 'Usuário agora é administrador!' : 'Usuário não é mais administrador!');
})->name('users.toggleAdmin');

Route::patch('/admin/users/{user}', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
    ]);
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }
    $user->save();
    return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
})->name('users.update');


Route::get('/', function () {
    return view('layout.base', [
        'page' => 'home',
    ]);
})->middleware(IsAuthenticated::class)->name('home');

Route::get('/temperature', [TemperatureController::class, 'show'])->middleware(IsAuthenticated::class)->name('temperature.index');

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
