@extends('layout.head')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">

<div class="auth-bg position-relative">
    <div class="auth-lang-select">
        <form id="langFormAuth" action="/lang/change" method="POST" style="display:inline-block;">
            @csrf
            @php
                $localeMap = [
                    'ar' => ['name' => 'AR', 'flag' => 'sa'],
                    'de' => ['name' => 'DE', 'flag' => 'de'],
                    'en' => ['name' => 'EN', 'flag' => 'us'],
                    'es' => ['name' => 'ES', 'flag' => 'es'],
                    'fr' => ['name' => 'FR', 'flag' => 'fr'],
                    'hi' => ['name' => 'HI', 'flag' => 'in'],
                    'it' => ['name' => 'IT', 'flag' => 'it'],
                    'ja' => ['name' => 'JA', 'flag' => 'jp'],
                    'ko' => ['name' => 'KO', 'flag' => 'kr'],
                    'pt-br' => ['name' => 'PT-BR', 'flag' => 'br'],
                    'ru' => ['name' => 'RU', 'flag' => 'ru'],
                    'zh' => ['name' => 'ZH', 'flag' => 'cn'],
                ];
                $supportedLocales = config('app.supported_locales', []);
            @endphp
            <select class="selectpicker" id="languageSelectAuth" name="locale">
                @foreach($supportedLocales as $locale)
                    @php $info = $localeMap[$locale] ?? ['name' => strtoupper($locale), 'flag' => $locale]; @endphp
                    <option value="{{ $locale }}" data-content='<span class="fi fi-{{ $info['flag'] }}"></span> {{ $info['name'] }}' {{ app()->getLocale() == $locale ? 'selected' : '' }}>{{ $info['name'] }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="auth-form-container">
        <img class="auth-logo" src="{{ asset('/assets/images/hospital-logo.png') }}" alt="Hospital Logo">
        <form action="{{ $action === 'login' ? route('login.submit') : route('register.submit') }}" method="POST" class="mt-4">
            @csrf
            @if($action === 'register')
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('auth.placeholder_name') }}" value="{{ old('name') }}">
                    <label for="name">{{ __('auth.name') }}</label>
                    @error('name')
                        <p class="text-danger small mt-1 mb-0 ms-2">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('auth.placeholder_email') }}" value="{{ old('email') }}">
                <label for="email">{{ __('auth.email') }}</label>
                @error('email')
                    <p class="text-danger small mt-1 mb-0 ms-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('auth.placeholder_password') }}">
                <label for="password">{{ __('auth.password') }}</label>
                @error('password')
                    <p class="text-danger small mt-1 mb-0 ms-2">{{ $message }}</p>
                @enderror
            </div>
            @if($action === 'register')
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('auth.placeholder_password') }}">
                    <label for="password_confirmation">{{ __('auth.password') }}</label>
                    @error('password_confirmation')
                        <p class="text-danger small mt-1 mb-0 ms-2">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            <p class="d-flex justify-content-between mx-2">@if ($action === 'register') {{ __('auth.has_acc') }} <a class="text-decoration-none" href="{{ route('login.form') }}">{{ __('auth.has_acc_link') }}</a> @elseif($action === 'login') {{ __('auth.no_acc') }} <a class="text-decoration-none" href="{{ route('register.form') }}">{{ __('auth.no_acc_link') }}</a> @endif</p>
            <button type="submit" class="btn btn-primary w-100">{{ $action === 'login' ? __('auth.login') : __('auth.register') }}</button>
        </form>
    </div>
</div>

<script src="{{ asset('js/pages/auth.js') }}" defer></script>
