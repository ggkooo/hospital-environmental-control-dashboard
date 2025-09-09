<link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end aside-custom" id="sidebar">
    <nav class="flex-grow-1">
        <ul class="sidebar-nav">
            <li class="nav-item"><a href="/" class="nav-link{{ request()->is('/') ? ' active' : '' }}">{{ __('sidebar.home') }}</a></li>
            <li class="nav-item"><a href="/temperature" class="nav-link{{ request()->is('temperature') ? ' active' : '' }}">{{ __('sidebar.temperature') }}</a></li>
            <li class="nav-item"><a href="/humidity" class="nav-link{{ request()->is('humidity') ? ' active' : '' }}">{{ __('sidebar.humidity') }}</a></li>
            <li class="nav-item"><a href="/noise" class="nav-link{{ request()->is('noise') ? ' active' : '' }}">{{ __('sidebar.noise') }}</a></li>
            <li class="nav-item"><a href="/relatorio" class="nav-link{{ request()->is('relatorio') ? ' active' : '' }}">{{ __('sidebar.report') }}</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" aria-expanded="false">
                    {{ __('sidebar.administration') }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="/admin/users">{{ __('sidebar.user_management') }}</a></li>
                    <li><a class="dropdown-item" href="/admin/reports">{{ __('sidebar.report_generation') }}</a></li>
                    <li><a class="dropdown-item" href="/admin/access">{{ __('sidebar.access_control') }}</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="sidebar-user d-flex align-items-center justify-content-between">
        <div id="sidebarUserToggle" class="d-flex align-items-center sidebar-user-toggle">
            <img src="https://placehold.co/32x32" alt="User" class="rounded-circle" width="32" height="32">
            <span class="ms-2">Giordano Berwig</span>
        </div>
        <div id="sidebarUserDropdown" class="sidebar-user-dropdown bg-white border shadow p-3">
            <a href="/profile" class="d-block mb-2 text-dark"><i class="bi bi-person me-2"></i>{{ __('sidebar.profile') }}</a>
            <a href="/settings" class="d-block mb-2 text-dark"><i class="bi bi-gear me-2"></i>{{  __('sidebar.settings') }}</a>
            <a href="/logout" class="d-block text-danger"><i class="bi bi-box-arrow-right me-2"></i>{{ __('sidebar.leave') }}</a>
        </div>
    </div>
    <div class="sidebar-lang-form d-block d-md-none mt-3">
        <form id="langFormSidebar" action="/lang/change" method="POST" style="display:inline-block; width:100%;">
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
            <select class="selectpicker" id="languageSelectSidebar" name="locale" style="width:100%;">
                @foreach($supportedLocales as $locale)
                    @php $info = $localeMap[$locale] ?? ['name' => strtoupper($locale), 'flag' => $locale]; @endphp
                    <option value="{{ $locale }}" data-content='<span class="fi fi-{{ $info['flag'] }}"></span> {{ $info['name'] }}' {{ app()->getLocale() == $locale ? 'selected' : '' }}>{{ $info['name'] }}</option>
                @endforeach
            </select>
        </form>
    </div>
</aside>
<script src="{{ asset('js/layout/sidebar.js') }}"></script>
