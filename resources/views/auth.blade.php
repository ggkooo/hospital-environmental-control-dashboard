<!-- jQuery (required for Bootstrap Select) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS (se ainda não estiver incluído) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<!-- Flag Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css">
<!-- Bootstrap Select CSS (for Bootstrap 5) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Select JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>

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
                    <input type="text" class="form-control" id="name" placeholder="{{ __('auth.placeholder_name') }}">
                    <label for="name">{{ __('auth.name') }}</label>
                </div>
            @endif
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="{{ __('auth.placeholder_email') }}">
                <label for="email">{{ __('auth.email') }}</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="{{ __('auth.placeholder_password') }}">
                <label for="password">{{ __('auth.password') }}</label>
            </div>
            @if($action === 'register')
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm_password" placeholder="{{ __('auth.placeholder_password') }}">
                    <label for="confirm_password">{{ __('auth.password') }}</label>
                </div>
            @endif
            <button type="submit" class="btn btn-primary w-100">{{ $action === 'login' ? __('auth.login') : __('auth.register') }}</button>
        </form>
    </div>
</div>
