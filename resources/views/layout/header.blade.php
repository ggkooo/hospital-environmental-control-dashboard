<body>
    <header class="navbar navbar-light bg-white border-bottom shadow-sm px-4 py-2 d-flex align-items-center" style="height: 10vh;">
        <span class="navbar-brand mb-0 h1"><img src="{{ asset('/assets/images/hospital-logo.png') }}" width="160px" alt=""></span>
        <div class="ms-auto">
            <select class="selectpicker" id="languageSelect" style="width: 170px;" data-width="170px">
                <option value="pt-br" data-content='<span class="fi fi-br"></span> PT-BR'>PT-BR</option>
                <option value="en" data-content='<span class="fi fi-us"></span> EN' selected>EN</option>
                <option value="es" data-content='<span class="fi fi-es"></span> ES'>ES</option>
                <option value="de" data-content='<span class="fi fi-de"></span> DE'>DE</option>
                <option value="it" data-content='<span class="fi fi-it"></span> IT'>IT</option>
                <option value="ru" data-content='<span class="fi fi-ru"></span> RU'>RU</option>
                <option value="hi" data-content='<span class="fi fi-in"></span> HI'>HI</option>
                <option value="ar" data-content='<span class="fi fi-sa"></span> AR'>AR</option>
                <option value="fr" data-content='<span class="fi fi-fr"></span> FR'>FR</option>
                <option value="zh" data-content='<span class="fi fi-cn"></span> ZH'>ZH</option>
                <option value="ja" data-content='<span class="fi fi-jp"></span> JA'>JA</option>
                <option value="ko" data-content='<span class="fi fi-kr"></span> KO'>KO</option>
            </select>
        </div>
    </header>
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
            var savedLang = localStorage.getItem('language');
            if (savedLang) {
                $('#languageSelect').selectpicker('val', savedLang);
            }
            // Salva o idioma no localStorage ao alterar
            $('#languageSelect').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                var selectedLang = $(this).val();
                localStorage.setItem('language', selectedLang);
                // location.reload(); // Removido para não recarregar a página
            });
        });
    </script>
</body>
