$(document).ready(function() {
    // Alle te hiden zaken, hiden indien dit niet het geval is
    $('.kj_extra_details, .kj_show_less').each(function() {
        $(this).hide();
    });

    // Click actions binden
    $('body').on('click', '.kj_show_more, .kj_show_less', function(e) {
        e.preventDefault();

        $(this).parent().find('.kj_extra_details').slideToggle();
        $(this).parent().find('.kj_show_more').toggle();
        $(this).parent().find('.kj_show_less').toggle();
    });
});