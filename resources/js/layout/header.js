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
