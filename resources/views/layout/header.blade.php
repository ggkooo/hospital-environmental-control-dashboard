<body>
    <header class="navbar navbar-light bg-white border-bottom shadow-sm px-4 py-2 d-flex align-items-center" style="height: 10vh;">
        <span class="navbar-brand mb-0 h1"><img src="{{ asset('/assets/images/hospital-logo.png') }}" width="160px" alt=""></span>
        <div class="ms-auto">
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
                <select class="selectpicker" id="languageSelect" name="locale" style="width: 170px;" data-width="170px">
                    @foreach($supportedLocales as $locale)
                        @php $info = $localeMap[$locale] ?? ['name' => strtoupper($locale), 'flag' => $locale]; @endphp
                        <option value="{{ $locale }}" data-content='<span class="fi fi-{{ $info['flag'] }}"></span> {{ $info['name'] }}' {{ app()->getLocale() == $locale ? 'selected' : '' }}>{{ $info['name'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </header>
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
            // Ao mudar o idioma, envia o formulário por AJAX e recarrega a página
            $('#languageSelect').on('changed.bs.select', function () {
                var selectedLang = $(this).val();
                var token = document.querySelector('#langFormHeader input[name=_token]').value;
                $.post({
                    url: '/lang/change',
                    data: { locale: selectedLang, _token: token },
                    success: function() {
                        window.location.reload();
                    }
                });
            });
        });
    </script>
</body>
