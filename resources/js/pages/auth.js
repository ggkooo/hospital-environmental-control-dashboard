$(function() {
    $('#languageSelectAuth').selectpicker();
    $('#languageSelectAuth').on('changed.bs.select', function() {
        $('#langFormAuth').submit();
    });
});
