<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth', [
            'action' => 'login',
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (auth()->attempt($validated)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login.form');
    }
}
