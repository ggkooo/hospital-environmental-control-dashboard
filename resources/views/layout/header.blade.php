<link rel="stylesheet" href="{{ asset('css/layout/header.css') }}">

<body>
    <header class="header d-flex align-items-center justify-content-between">
        <span class="header__logo"><img src="{{ asset('/assets/images/hospital-logo.png') }}" alt="Hospital Environmental Control"></span>
        <div class="header__lang-form d-none d-md-block">
            <form id="langFormHeader" action="/lang/change" method="POST" style="display:inline-block;">
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
                <select class="selectpicker" id="languageSelect" name="locale">
                    @foreach($supportedLocales as $locale)
                        @php $info = $localeMap[$locale] ?? ['name' => strtoupper($locale), 'flag' => $locale]; @endphp
                        <option value="{{ $locale }}" data-content='<span class="fi fi-{{ $info['flag'] }}"></span> {{ $info['name'] }}' {{ app()->getLocale() == $locale ? 'selected' : '' }}>{{ $info['name'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <button id="sidebarHamburger" class="btn btn-light d-md-none ms-auto" aria-label="Abrir menu" style="width:40px;height:40px;">
            <i class="bi bi-list" style="font-size:1.5rem;"></i>
        </button>
    </header>
    
    <script src="{{ asset('js/layout/header.js') }}"></script>
